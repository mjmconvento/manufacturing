<?php

namespace Catalyst\ServiceBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController2;
use Catalyst\ServiceBundle\Entity\ServiceOrder;
use Catalyst\ServiceBundle\Entity\SVEntry;
use Catalyst\ServiceBundle\Entity\SVETask;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\UserBundle\Entity\User;
use Catalyst\ValidationException;
use Doctrine\ORM\EntityManager;
use DateTime;
use FPDF;

class ServiceOrderController extends CrudController2
{
    public function __construct()
    {
        $this->route_prefix = 'cat_service_so';
        $this->title = 'Service Order';

        $this->list_title = 'Service Orders';
        $this->list_type = 'dynamic';
    }


    public function indexAction()
    {
        $this->checkAccess($this->route_prefix . '.view');

        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'LeatherServiceBundle:ServiceOrder:index.html.twig';


        $params['date_from'] = new DateTime('now');
        $params['date_to'] = new DateTime('now');
        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
    }

    protected function setupGridLoader()
    {
        $data = $this->getRequest()->query->all();
        $grid = $this->get('catalyst_grid');

        // loader
        $gloader = $grid->newLoader();
        $gloader->processParams($data)
            ->setRepository($this->repo);

        // grid joins
        $gjoins = $this->getGridJoins();
        foreach ($gjoins as $gj)
            $gloader->addJoin($gj);

        // grid columns
        $gcols = $this->getGridColumns();

        // add action column if it's dynamic
        if ($this->list_type == 'dynamic')
            $gcols[] = $grid->newColumn('', 'getID', null, 'o', array($this, 'callbackGrid'), false, false);

        // add columns
        foreach ($gcols as $gc)
            $gloader->addColumn($gc);

        return $gloader;
    }

    protected function newBaseClass()
    {
        return new ServiceOrder();
    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getCode();
    }

    protected function padGridParams(&$params, $id)
    {
        $params['table_can_edit'] = true;
        $params['table_can_delete'] = false;

        return $params;
    }

    protected function getGridJoins()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newJoin('c', 'customer', 'getCustomer'),
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
            $grid->newColumn('Customer', 'getName', 'name', 'c'),
            $grid->newColumn('Price', 'getTotalPrice', 'total_price'),
            $grid->newColumn('Status', 'getStatusFormatted', 'status_id'),
        );
    }

    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();

        // new, set blank code for now
        if ($is_new)
        {
            $o->setUser($this->getUser());
            $o->setCode('');
        }
        $status = $data['status_id'];
        $super_admin = $data['super_admin'];

        // set note
        $o->setNote($data['note']);

        // set dates
        $o->setDateIssue(new DateTime($data['date_issue']));
        $o->setDateNeed(new DateTime($data['date_need']));
        $o->setDateNeedRepairman(new DateTime($data['date_need_repairman']));

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

        // product
        $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($data['product_id']);
        if ($prod == null)
            throw new ValidationException('Could not find product.');
        $o->setProduct($prod);


        // update entries
        if ($status == "Issued" or $status == "Received" or $super_admin == "super_admin")
        {
        $this->updateEntries($o, $data, $is_new);      
        }

        // generate code
        if ($is_new)
        {
            // persist and flush to get autoincrement id
            $em->persist($o);
            $em->flush();

            // generate code
            $o->setCode($o->getDateCreate()->format('ymd') . '-' . sprintf("%02d-%06d", $o->getWarehouse()->getInternalCode(), $o->getID()));
            $em->flush();
        }
    }

    protected function updateEntries($o, $data, $is_new)
    {
        $em = $this->getDoctrine()->getManager();

        // clear entries
        $ents = $o->getEntries();
        foreach ($ents as $ent)
            $em->remove($ent);
        $o->clearEntries();

        // entry array for tracking which sve task goes to
        $all_entries = array();

        // entries
        if (isset($data['en_prod_id']))
        {
            foreach ($data['en_prod_id'] as $index => $prod_id)
            {
                // fields
                $qty = $data['en_qty'][$index];
                $price = $data['en_price'][$index];
                $user_id = $data['en_assigned_id'][$index];

                // product
                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                if ($prod == null)
                    throw new ValidationException('Could not find product.');

                $user = $em->getRepository('CatalystUserBundle:User')->find($user_id);

                // instantiate
                $entry = new SVEntry();
                $entry->setProduct($prod)
                    ->setQuantity($qty)
                    ->setPrice($price)
                    ->setServiceOrder($o)
                    ->setAssignedUser($user);

                // store in entry array
                $all_entries[$index] = $entry;
                

                // clear sve entries
                $ents = $entry->getTasks();
                foreach ($ents as $ent)
                    $em->remove($ent);
                $entry->clearTasks();


                // add entry
                $o->addEntry($entry);
            }
        }

        $this->updateTasks($all_entries, $data);
    }

    protected function updateTasks($all_entries, $data)
    {
        $em = $this->getDoctrine()->getManager();

        // check if we have any tasks to update
        if (!isset($data['svet_index']))
            return;


        // iterate through tasks
        foreach ($data['svet_index'] as $index => $svet_index)
        {
            // get entry
            $entry = $all_entries[$svet_index];

            // fields
            $id = $data['svet_id'][$index];
            $name = $data['svet_name'][$index];
            $sell_price = $data['svet_sell_price'][$index];
            $cost_price = $data['svet_cost_price'][$index];

            // assigned user
            if ($data['svet_assigned_id'][$index] == 0)
                $user = null;
            else
            {
                $user = $em->getRepository('CatalystUserBundle:User')
                    ->find($data['svet_assigned_id'][$index]);

                // user not found
                if ($user == null)
                    throw new ValidationException('Could not find assigned user.');
            }

            // instantiate svetask
            $sve_task = new SVETask();
            $sve_task->setName($name)
                ->setSellPrice($sell_price)
                ->setCostPrice($cost_price)
                ->setAssignedUser($user);

            // add svetask to sv entry
            $entry->addTask($sve_task);
        }
    }

    protected function padFormParams(&$params, $so = null)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');

        // customer
        $custs = $em->getRepository('CatalystSalesBundle:Customer')->findAll();
        $cust_opts = array();
        foreach ($custs as $cust)
            $cust_opts[$cust->getID()] = $cust->getName();
        $params['cust_opts'] = $cust_opts;

        /*
        // user
        $users = $em->getRepository('CatalystUserBundle:User')->findAll();
        $user_opts = array();
        foreach ($users as $user)
            $user_opts[$user->getID()] = $user->getUserName();
        $params['user_opts'] = $user_opts;
        */

        // get services that can be sold
        $params['prod_opts'] = $inv->getProductOptions(array('flag_sale' => true, 'flag_service' => true));

        // get services that can be sold
        $prodgroup = $this->get('catalyst_configuration');
        $params['cprod_opts'] = $inv->getProductOptions(array('prodgroup_id' => $prodgroup->get('catalyst_product_group_default')));

        // warehouse
        $params['wh_opts'] = $inv->getWarehouseOptions(array('flag_shopfront' => true));

        // virtual warehouse
        $params['v_wh_opts'] = $inv->getWarehouseOptions(array('type_id' => 'virtual'));

        // product group
        $params['pg_opts'] = $inv->getProductGroupOptions();
        $params['pg_default'] = $this->get('catalyst_configuration')->get('catalyst_product_group_default');

        // repair users
        $repuser_opts = array(0 => '[ Select Repairman ]');
        $um = $this->get('catalyst_user');
        $config = $this->get('catalyst_configuration');
        $user_group = $um->findGroup($config->get('catalyst_service_role_default'));
        $users = $user_group->getUsers();
        foreach ($users as $repuser)
            $repuser_opts[$repuser->getID()] = $repuser->getUsername();
        $params['repuser_opts'] = $repuser_opts;

        // params for product optional opts
        $brand_opts = array(0 => '[ Select Brand ]');
        $color_opts = array(0 => '[ Select Color ]');
        $material_opts = array(0 => '[ Select Material ]');
        $condition_opts = array(0 => '[ Select Condition ]');

        $params['brand_opts'] = $brand_opts + $this->getFieldOptions('PBrand');
        $params['color_opts'] = $color_opts + $this->getFieldOptions('PColor');
        $params['material_opts'] = $material_opts + $this->getFieldOptions('PMaterial');
        $params['condition_opts'] = $condition_opts + $this->getFieldOptions('PCondition');

        return $params;
    }

    protected function getFieldOptions($repo)
    {
        // brand
        $em = $this->getDoctrine()->getManager();
        $objects = $em->getRepository('CatalystInventoryBundle:' . $repo)->findAll();
        $opts = array();
        foreach ($objects as $o)
            $opts[$o->getId()] = $o->getName();

        return $opts;
    }

    protected function statusUpdate(ServiceOrder $so, $status)
    {
        $em = $this->getDoctrine()->getManager();
        $so->setStatus($status);
        $em->flush();

        $this->addFlash('success', 'Service order ' . $so->getCode() . ' status has been updated to ' . $status . '.');

        return $this->redirect($this->generateUrl('cat_service_so_edit_form', array('id' => $so->getID())));
    }

    public function statusChangeAction($id, $status_id)
    {
        // TODO: check validity of status id

        // find service order
        $em = $this->getDoctrine()->getManager();
        $so = $em->getRepository('CatalystServiceBundle:ServiceOrder')->find($id);


        $so_data = $so->getData(); 
        $unpaid = $so_data["bal_status"];
        $status = $so->getStatus();

        //check all repairman if unassigned
        $norepairemen = false;
        foreach ($so->getEntries() as $entry)
        {
            if($entry->getAssignedID() == null)
            {
                $norepairemen = true;
            }
        } 
        
        $entries_length = count($so->getEntries());

        //no service entry validation
        if($entries_length == 0 && $status == "received")
        {
            $this->addFlash('error', 'Service order ' . $so->getCode() . ' No Service Order Entries');
            return $this->redirect($this->generateUrl('cat_service_so_edit_form', array('id' => $so->getID())));
        }

        //no repairman validation
        if($norepairemen == true  && $status == "received")
        {
            $this->addFlash('error', 'Service order ' . $so->getCode() . ' No repairman assigned');
            return $this->redirect($this->generateUrl('cat_service_so_edit_form', array('id' => $so->getID())));
        }

        //validation if finished but unpaid
        if ($unpaid == "unpaid" && $status == "finished")
        {
            $this->addFlash('error', 'Service order ' . $so->getCode() . ' balance is unpaid ');
            return $this->redirect($this->generateUrl('cat_service_so_edit_form', array('id' => $so->getID())));
        }


        switch ($status_id)
        {
            case ServiceOrder::STATUS_RECEIVE:
                $this->statusReceive($so);
                break;
            case ServiceOrder::STATUS_ASSIGN:
                $this->statusAssign($so);
                break;
            case ServiceOrder::STATUS_CLAIM:
                $this->statusClaim($so);
                break;
        }

        return $this->statusUpdate($so, $status_id);
    }

    protected function statusReceive(ServiceOrder $so)
    {
        // get source and destination warehouses
        $dest_wh = $so->getWarehouse();
        $source_wh = $so->getCustomer()->getWarehouse();
        $prod = $so->getProduct();

        $inv = $this->get('catalyst_inventory');

        // setup inventory transaction
        $trans = $inv->newTransaction();
        $trans->setDescription('Received customer item for service order - ' . $so->getCode() . '.');
        $trans->setUser($this->getUser());

        // source entry
        $source_entry = $inv->newEntry();
        $source_entry->setCredit(1)
            ->setWarehouse($source_wh)
            ->setProduct($prod);
        $trans->addEntry($source_entry);

        // destination entry
        $dest_entry = $inv->newEntry();
        $dest_entry->setDebit(1)
            ->setWarehouse($dest_wh)
            ->setProduct($prod);
        $trans->addEntry($dest_entry);

        // persist
        $inv->persistTransaction($trans);
    }

    protected function statusAssign(ServiceOrder $so)
    {
    //     $em = $this->getDoctrine()->getManager();
    //     $user_id = $this->getRequest()->query->get('user_id');
    //     $user = $em->getRepository('CatalystUserBundle:User')->find($user_id);

    //     $sv->setAssignedUser($user);
    //     $so->canService(true);
    }

    protected function statusClaim(ServiceOrder $so)
    {
        // get source and destination warehouses
        $source_wh = $so->getWarehouse();
        $dest_wh = $so->getCustomer()->getWarehouse();
        $prod = $so->getProduct();

        $inv = $this->get('catalyst_inventory');

        // setup inventory transaction
        $trans = $inv->newTransaction();
        $trans->setDescription('Customer claimed item for service order - ' . $so->getCode() . '.');
        $trans->setUser($this->getUser());

        // source entry
        $source_entry = $inv->newEntry();
        $source_entry->setCredit(1)
            ->setWarehouse($source_wh)
            ->setProduct($prod);
        $trans->addEntry($source_entry);

        // destination entry
        $dest_entry = $inv->newEntry();
        $dest_entry->setDebit(1)
            ->setWarehouse($dest_wh)
            ->setProduct($prod);
        $trans->addEntry($dest_entry);

        // persist
        $inv->persistTransaction($trans);
    }


    protected function getStockReport()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('select so.code, LOWER(so.date_issue), c.name as c_name, so.total_price, so.status_id  from Catalyst\ServiceBundle\Entity\ServiceOrder so join so.customer c  Order by so.id asc');              
        return $query->getResult();
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

            if ($data['bal_amount'] < 0 )
            {
                $this->addFlash('error', $this->title . ' ' . $this->getObjectLabel($object) . ' Balance is less than 0.');
            }
            else
            {
                $this->addFlash('success', $this->title . ' ' . $this->getObjectLabel($object) . ' edited successfully.');
                $this->update($object, $data);
                $odata = $object->toData();
                $this->logUpdate($odata);
            }
            
            $em->flush();
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

}
