<?php

namespace Catalyst\PurchasingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\PurchasingBundle\Entity\PurchaseRequest;
use Catalyst\PurchasingBundle\Entity\PREntry;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\ValidationException;
use DateTime;

class PurchaseRequestController extends CrudController
{
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
        return new PurchaseRequest();
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
            $grid->newColumn('Requisition No', 'getCode', 'code'),
            $grid->newColumn('Date Issued', 'getDateCreate', 'date_create', 'o', array($this, 'formatDate')),
            $grid->newColumn('Reference No', 'getReferenceNum', 'total_price'),
            $grid->newColumn('Department', 'getDepartment', 'total_price'),
            $grid->newColumn('Date Needed', 'getDateNeeded', 'date_needed', 'o', array($this, 'formatDate')),
        );
    }

    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();

        // validate code
        if (strlen($data['code']) > 0)
            $o->setCode($data['code']);
        else
            throw new ValidationException('Cannot leave code blank');

        $o->setDateIssue(new DateTime($data['date_issue']));

        // supplier
        $supp = $em->getRepository('CatalystPurchasingBundle:Supplier')->find($data['supplier_id']);
        if ($supp == null)
            throw new ValidationException('Could not find supplier.');
        
        $o->setSupplier($supp);

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

        // suppplier
        $params['prod_opts'] = $inv->getProductOptions(array('flag_purchase' => true));
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

    public function editFormAction($id)
    {
        $this->checkAccess($this->route_prefix . '.view');

        $this->hookPreAction();
        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository($this->repo)->find($id);

        $params = $this->getViewParams('Edit');
        $params['object'] = $obj;
        $params['o_label'] = $this->getObjectLabel($obj);

        // Getting super user
        $prodgroup = $this->get('catalyst_configuration');
        $repository = $this->getDoctrine()
            ->getRepository('CatalystUserBundle:Group');
        $super_user = $repository->findOneById($prodgroup->get('catalyst_super_user_role_default'));    
        $params['super_user'] = $super_user;

        // check if we have access to form
        $params['readonly'] = !$this->getUser()->hasAccess($this->route_prefix . '.edit');

        $this->padFormParams($params, $obj);

        return $this->render('CatalystTemplateBundle:Object:edit.html.twig', $params);
    }

    public function editSubmitAction($id)
    {
        $this->checkAccess($this->route_prefix . '.edit');

        $this->hookPreAction();
        try
        {
            $em = $this->getDoctrine()->getManager();
            $data = $this->getRequest()->request->all();

            $object = $em->getRepository($this->repo)->find($id);
            $object->setStatus($data['status_id']);            

            $this->update($object, $data);

            $em->flush();

            $odata = $object->toData();
            $this->logUpdate($odata);

            $this->addFlash('success', $this->title . ' ' . $this->getObjectLabel($object) . ' edited successfully.');

            return $this->redirect($this->generateUrl($this->getRouteGen()->getEdit(), array('id' => $id)));
        }
        catch (ValidationException $e)
        {
            $this->addFlash('error', $e->getMessage());
            return $this->editError($object, $id);
        }
        catch (DBALException $e)
        {
            $this->addFlash('error', 'Database error encountered. Possible duplicate.');
            error_log($e->getMessage());
            return $this->addError($object);
        }
    }

    public function addFormAction()
    {
        $this->checkAccess($this->route_prefix . '.add');

        $this->hookPreAction();
        $obj = $this->newBaseClass();

        $params = $this->getViewParams('Add');
        $params['object'] = $obj;


        // Getting super user
        $prodgroup = $this->get('catalyst_configuration');
        $repository = $this->getDoctrine()
            ->getRepository('CatalystUserBundle:Group');
        $super_user = $repository->findOneById($prodgroup->get('catalyst_super_user_role_default'));    
        $params['super_user'] = $super_user;


        // check if we have access to form
        $params['readonly'] = !$this->getUser()->hasAccess($this->route_prefix . '.add');

        $this->padFormParams($params, $obj);

        return $this->render('CatalystTemplateBundle:Object:add.html.twig', $params);
    }


}
