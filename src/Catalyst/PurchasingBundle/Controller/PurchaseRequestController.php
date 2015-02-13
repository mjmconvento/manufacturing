<?php

namespace Catalyst\PurchasingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\PurchasingBundle\Entity\PurchaseRequest;
use Catalyst\PurchasingBundle\Entity\PREntry;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\ValidationException;
use DateTime;

class PurchaseRequestController extends CrudController
{
    use TrackCreate;
    public function __construct()
    {
        $this->route_prefix = 'cat_pur_pr';
        $this->title = 'Purchase Request';

        $this->list_title = 'Purchase Requests';
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
        $obj = new PurchaseRequest();
        return $obj;
    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getCode();
    }

    public function formatDate($date)
    {
        return $date->format('m/d/Y');
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Request No', 'getCode', 'code'),
            $grid->newColumn('Date Issued', 'getDateCreate', 'date_create', 'o', array($this, 'formatDate')),
            $grid->newColumn('Reference No', 'getReferenceCode', 'total_price'),
            $grid->newColumn('Department', 'getDepartment', 'total_price'),
            $grid->newColumn('Date Needed', 'getDateNeeded', 'date_needed', 'o', array($this, 'formatDate')),
        );
    }

    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();
        $o->setDateNeeded(new DateTime($data['date_need']));
        $o->setPurpose($data['purpose']);
        $o->setNotes($data['notes']);
        $o->setReferenceCode($data['reference_code']);
        $o->setDepartment($data['department']);
        $this->updateTrackCreate($o,$data,$is_new);
        // clear entries
        $ents = $o->getEntries();
        foreach($ents as $ent)
            $em->remove($ent);
        $o->clearEntries();

        // entries
        if (isset($data['en_prod_id']))
        {
            foreach ($data['en_prod_id'] as $index => $prod_id)
            {
                // fields
                $qty = $data['en_qty'][$index];
                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                if ($prod == null)
                    throw new ValidationException('Could not find product.');

                // instantiate
                $entry = new PREntry();
                $entry->setProduct($prod)
                    ->setQuantity($qty)
                    ->setUserCreate($this->getUser());

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

        // suppplier
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

        $log->log('cat_pur_po_status_update', 'status updated for Purchase Order ' . $po->getID() . '.', $po->toData());

        $this->addFlash('success', 'Purchase order ' . $po->getCode() . ' status has been updated to ' . $status . '.');

        return $this->redirect($this->generateUrl('cat_pur_po_edit_form', array('id' => $id)));
    }

    protected function hookPostSave($obj,$is_new = false) 
    {
        $em = $this->getDoctrine()->getManager();
        if($is_new){
            $obj->generateCode();
            $em->persist($obj);
            $em->flush();
        }
       
    }


}
