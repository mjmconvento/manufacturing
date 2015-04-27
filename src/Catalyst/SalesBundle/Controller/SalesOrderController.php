<?php

namespace Catalyst\SalesBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\SalesBundle\Entity\SalesOrder;
use Catalyst\SalesBundle\Entity\SOEntry;
use Catalyst\ValidationException;
use Catalyst\SalesBundle\Model\SalesOrderPDF;
use DateTime;

class SalesOrderController extends CrudController
{
    public function __construct()
    {
        
        $this->route_prefix = 'cat_sales_so';
        $this->title = 'Sales Order';

        $this->list_title = 'Sales Orders';
        $this->list_type = 'dynamic';
    }

    public function indexAction()
    {
        $this->checkAccess($this->route_prefix . '.view');

        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'CatalystTemplateBundle::index.csv.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
    }

    protected function newBaseClass()
    {
        $so = new SalesOrder();
        $so->setUser($this->getUser());
        return $so;
    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getCode();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Code', 'getCode', 'code'),
            $grid->newColumn('Date Issued', 'getDateIssueText', 'date_issue'),
            $grid->newColumn('Customer', 'getDisplayName', 'last_name', 'c'),
            $grid->newColumn('Issued By', 'getUserName', 'username', 'u'),
            $grid->newColumn('Status', 'getStatusFormatted', 'status_id'),
        );
    }

    protected function getGridJoins()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newJoin('u', 'user', 'getUser'),
            $grid->newJoin('c', 'customer', 'getCustomer'),
        );
    }

    protected function padGridParams(&$params, $id)
    {
        $params['table_can_edit'] = true;
        $params['table_can_delete'] = false;

        return $params;
    }

    protected function padFormParams(&$params, $o = null)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        $sm = $this->get('catalyst_sales');

        $params['wh_opts'] = $inv->getWarehouseOptions(array('flag_shopfront' => true));
        $params['prod_opts'] = $inv->getProductOptions(array('flag_sale' => true, 'flag_service' => false));
        $params['cust_opts'] = $sm->getCustomerOptions();
        $params['v_wh_opts'] = $inv->getWarehouseOptions();        

        // payment method
        $pms = $em->getRepository('CatalystSalesBundle:PaymentMethod')->findAll();
        $pm_opts = array();
        foreach ($pms as $pm)
            $pm_opts[$pm->getID()] = $pm->getName();
        $params['pm_opts'] = $pm_opts;

        // users options
        $users = $em->getRepository('CatalystUserBundle:User')->findAll();
        $users_opts = array();
        foreach ($users as $user)
            $users_opts[$user->getID()] = $user->getName();
        $params['users_opts'] = $users_opts;

        $params['status_opts'] = array(
            'draft' => 'Draft',
            'approved' => 'Approved',
            'cancel' => 'Cancel',
        );

        return $params;
    }

    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();

        if ($is_new)
            $o->setUser($this->getUser());

        $o->setCode("");
        $o->setTax($data['tax']);
        $o->setDownpayment($data['downpayment']);
        $o->setBalance($data['balance']);
        $o->setDateIssue(new DateTime($data['date_issue']));


        // customer 
        $cust = $em->getRepository('CatalystSalesBundle:Customer')->find($data['customer_id']);
        if ($cust == null)
            throw new ValidationException('Could not find customer.');
        $o->setCustomer($cust);
        
        // warehouse
        $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($data['warehouse_id']);
        if ($wh == null)
            throw new ValidationException('Could not find warehouse.');
        $o->setWarehouse($wh);

        // payment method
        $payment_id = $em->getRepository('CatalystSalesBundle:PaymentMethod')->find($data['payment_id']);
        if ($payment_id == null)
            throw new ValidationException('Could not find payment method.');
        $o->setPaymentMethod($payment_id);

        // generate code
        $em->persist($o);
        $em->flush();
        $o->setCode('SO-' . sprintf("%02d-%06d", $o->getWarehouse()->getInternalCode(), $o->getID()));
  
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

                // product
                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                if ($prod == null)
                    throw new ValidationException('Could not find product.');

                // instantiate
                $entry = new SOEntry();
                $entry->setProduct($prod)
                    ->setQuantity($qty)
                    ->setPrice($price);

                // add entry
                $o->addEntry($entry);
            }
        }
    }

    protected function buildData($o)
    {
        $data = array(
            'id' => $o->getID(),
            'name' => $o->getDisplayName(),
            'email' => $o->getEmail(),
            'notes' => $o->getNotes(),
        );

        return $data;
    }

    protected function statusUpdate(SalesOrder $so, $status)
    {
        $em = $this->getDoctrine()->getManager();
        $so->setStatus($status);
        $em->flush();

        $this->addFlash('success', 'Sales order ' . $so->getCode() . ' status has been updated to ' . $status . '.');

        return $this->redirect($this->generateUrl('cat_sales_so_edit_form', array('id' => $so->getID())));
    }

    public function statusChangeAction($id, $status_id)
    {
        // TODO: check validity of status id

        // find sales order
        $em = $this->getDoctrine()->getManager();
        $so = $em->getRepository('CatalystSalesBundle:SalesOrder')->find($id);
        if ($so == null)
            throw new ValidationError('Cannot find sales order.');

        switch ($status_id)
        {
            case SalesOrder::STATUS_APPROVE:
                $this->statusApprove($so);
                break;
            case SalesOrder::STATUS_CANCEL:
                $this->statusCancel($so);
                break;
        }

        return $this->statusUpdate($so, $status_id);
    }

    protected function statusApprove(SalesOrder $so)
    {
        $em = $this->getDoctrine()->getManager();

        // get source and destination warehouses
        $source_wh = $so->getWarehouse();
        $dest_wh = $so->getCustomer()->getWarehouse();
        $entries = $so->getEntries();

        $inv = $this->get('catalyst_inventory');

        // setup inventory transaction
        $trans = $inv->newTransaction();
        $trans->setDescription('Received customer item for sales order - ' . $so->getCode() . '.');
        $trans->setUser($this->getUser());

        foreach($entries as $entry)
        {
            $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($entry->getProduct()->getID());

            // source entry
            $source_entry = $inv->newEntry();
            $source_entry->setCredit($entry->getQuantity())
                ->setWarehouse($source_wh)
                ->setProduct($prod);
            $trans->addEntry($source_entry);

            // destination entry
            $dest_entry = $inv->newEntry();
            $dest_entry->setDebit($entry->getQuantity())
                ->setWarehouse($dest_wh)
                ->setProduct($prod);
            $trans->addEntry($dest_entry);
        }

        // persist
        $inv->persistTransaction($trans);
    }

    protected function statusCancel(SalesOrder $so)
    {
    }

//    protected function getStockReport()
//    {
//        $em = $this->getDoctrine()->getManager();
//        $query = $em->createQuery('select so.code, LOWER(so.date_issue), c.name as c_name, u.name as u_name, so.status_id from Catalyst\SalesBundle\Entity\SalesOrder so join so.customer c join so.user u Order by so.id asc');              
//        return $query->getResult();
//    }    

    public function printJobOrderAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $so = $em->getRepository('CatalystSalesBundle:SalesOrder')
            ->find($id);
        $pm = $em->getRepository('CatalystSalesBundle:PaymentMethod')
            ->findAll();

        $so_pdf = new SalesOrderPDF();
        $so_pdf->generate($so, $pm);
    }


    public function addSubmitAction()
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

    public function addFormAction()
    {
        $this->checkAccess($this->route_prefix . '.add');
        $this->hookPreAction();
        $obj = $this->newBaseClass();

        $params = $this->getViewParams('Add');
        $params['object'] = $obj;

        // check if we have access to form
        $params['readonly'] = !$this->getUser()->hasAccess($this->route_prefix . '.add');
        $this->padFormParams($params, $obj);
        return $this->render('CatalystTemplateBundle:Object:add.html.twig', $params);
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
            $object->setUserID($data['issued_by']);
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

}
