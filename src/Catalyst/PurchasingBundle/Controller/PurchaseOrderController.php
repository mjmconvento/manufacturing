<?php

namespace Catalyst\PurchasingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
use Catalyst\PurchasingBundle\Entity\POEntry;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\ValidationException;
use DateTime;

class PurchaseOrderController extends CrudController
{
    use TrackCreate;
    public function __construct()
    {
        $this->route_prefix = 'cat_pur_po';
        $this->title = 'Purchase Order';

        $this->list_title = 'Purchase Orders';
        $this->list_type = 'dynamic';
    }

    public function addEntryAction($entry_id)
    {
        $em = $this->getDoctrine()->getManager();

        $this->checkAccess($this->route_prefix . '.add');

        $this->hookPreAction();
        $obj = $this->newBaseClass();

        $params = $this->getViewParams('Add');

        // $inv = $this->get('catalyst_inventory');
        $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($entry_id);

        $params['prod'] = array(
                'id' => $prod->getID(),
                'code' => $prod->getCode(),
                'name' => $prod->getName(),
                'price' => $prod->getPriceSale()
            );

        $params['object'] = $obj;

        // check if we have access to form
        $params['readonly'] = !$this->getUser()->hasAccess($this->route_prefix . '.add');

        $this->padFormParams($params, $obj);

        return $this->render('CatalystTemplateBundle:Object:add.html.twig', $params);
    }

    public function addEntrySubmitAction()
    {
        $this->checkAccess($this->route_prefix . '.add');

        $this->hookPreAction();
        try
        {
            $em = $this->getDoctrine()->getManager();
            $data = $this->getRequest()->request->all();

            $obj = $this->newBaseClass();

            $this->update($obj, $data, true);

            $em->persist($obj);
            $em->flush();

            $odata = $obj->toData();
            $this->logAdd($odata);

            $this->addFlash('success', $this->title . ' added successfully.');

            return $this->redirect($this->generateUrl($this->getRouteGen()->getList()));
        }
        catch (ValidationException $e)
        {
            $this->addFlash('error', $e->getMessage());
            return $this->addError($obj);
        }
        catch (DBALException $e)
        {
            $this->addFlash('error', 'Database error encountered. Possible duplicate.');
            error_log($e->getMessage());
            return $this->addError($obj);
        }
    }

    protected function newBaseClass()
    {
        return new PurchaseOrder();
    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getCode();
    }

    protected function getGridJoins()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newJoin('s', 'supplier', 'getSupplier'),
        );
    }

    public function formatDate($date)
    {
        return $date->format('m/d/Y');
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Code', 'getCode', 'code'),
            $grid->newColumn('Date Issued', 'getDateIssue', 'date_issue', 'o', array($this, 'formatDate')),
            $grid->newColumn('Suppplier', 'getDisplayName', 'name', 's'),
            $grid->newColumn('Price', 'getTotalPrice', 'total_price'),
            $grid->newColumn('Status', 'getStatusFormatted', 'status_id'),
        );
    }

    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();
        $pur = $this->get('catalyst_purchasing');
        
        $o->setDateIssue(new DateTime($data['date_issue']));
        $o->setDateNeeded(new DateTime($data['date_need']));
        $o->setReferenceCode($data['reference_code']);
        $o->setSupplier($pur->getSupplier($data['supplier_id']));
        $o->setStatus($data['status_id']);            
        $this->updateTrackCreate($o,$data,$is_new);
        // clear entries
        $ents = $o->getEntries();
        foreach ($ents as $ent)
            $em->remove($ent);
        $o->clearEntries();

        // entries
        if (isset($data['en_prod_id']))
        {
            foreach ($data['en_prod_id'] as $index => $prod_id)
            {
                // fields
                $qty = $data['en_qty'][$index];
                $price = $data['en_price'][$index];
                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                if ($prod == null)
                    throw new ValidationException('Could not find product.');

                // instantiate
                $entry = new POEntry();
                $entry->setProduct($prod)
                    ->setQuantity($qty)
                    ->setPrice($price);

                // add entry
                $o->addEntry($entry);
            }
        }
        // TODO: not sure if product entry is needed to have atleast 1, will add an else statement with validation exception for product entry.
    }

    protected function padFormParams(&$params, $po = null)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        $pur = $this->get('catalyst_purchasing');
        // suppplier
        $params['supp_opts'] = $pur->getSupplierOptions();

        $params['status_opts'] = array(
            'draft' => 'Draft',
            'approved' => 'Approved',
            'fulfilled' => 'Fulfilled',
            'sent' => 'Sent',
            'cancelled' => 'Cancel',
        );
        
        $params['prod_opts'] = $inv->getProductOptions();

        return $params;
    }

    protected function statusUpdate($id, $status)
    {
        $log = $this->get('catalyst_log');
        $em = $this->getDoctrine()->getManager();
        $po = $em->getRepository('CatalystPurchasingBundle:PurchaseOrder')->find($id);
        if ($po == null)
            throw new ValidationError('Cannot find purchase order.');

        $po->setStatus($status);
        $em->flush();

        $log->log('cat_pur_po_status_update', 'status updateed for Purchase Order ' . $po->getID() . '.', $po->toData());

        $this->addFlash('success', 'Purchase order ' . $po->getCode() . ' status has been updated to ' . $status . '.');

        return $this->redirect($this->generateUrl('cat_pur_po_edit_form', array('id' => $id)));
    }

    public function statusApproveAction($id)
    {
        return $this->statusUpdate($id, PurchaseOrder::STATUS_APPROVED);
    }

    public function statusSendAction($id)
    {
        return $this->statusUpdate($id, PurchaseOrder::STATUS_SENT);
    }

    public function statusCancelAction($id)
    {
        return $this->statusUpdate($id, PurchaseOrder::STATUS_CANCEL);
    }

    public function statusFulfillAction($id)
    {
        return $this->statusUpdate($id, PurchaseOrder::STATUS_FULFILLED);
    }
    
    protected function hookPostSave($obj,$is_new = false) 
    {
        $em = $this->getDoctrine()->getManager();
        if($is_new){
            $obj->generateCode();
            $obj->setUserCreate($this->getUser());
            $em->persist($obj);
            $em->flush();
        }
       
    }

}
