<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Serenitea\OrderBundle\Entity\RequestOrder;
use Serenitea\OrderBundle\Entity\ROEntry;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\ValidationException;
use DateTime;

class RequestOrderController extends CrudController
{
    use TrackCreate;
    public function __construct()
    {
        $this->route_prefix = 'ser_pur_pr';
        $this->title = 'Requested Item';

        $this->list_title = 'Requested Items';
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
        $obj = new RequestOrder();
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
            $grid->newColumn('PO #', 'getCode', 'code'),
            $grid->newColumn('Date Issued', 'getDateCreate', 'date_issue', 'o', array($this, 'formatDate')),            
            $grid->newColumn('Target Delivery Date', 'getDateNeeded', 'date_needed', 'o', array($this, 'formatDate')),
            $grid->newColumn('Status', 'getStatusFormatted', 'status_id'),
        );
    }

    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();
        $o->setDateIssue(new DateTime($data['date_issue']));
        $o->setDateNeeded(new DateTime($data['date_need']));

        //set warehouse
        $id = $this->getUser()->getID();
        $user_data = $em->getRepository('CatalystUserBundle:User')->find($id);
        $warehouse_id = $user_data->getWarehouse();
        $o->setWarehouse($warehouse_id);

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
                // $price = $data['en_price'][$index];
                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                if ($prod == null)
                    throw new ValidationException('Could not find product.');

                // instantiate
                $entry = new ROEntry();
                $entry->setProduct($prod)
                    ->setQuantity($qty);
                    // ->setPrice($price);

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

        //get branch of user logged in
        $id = $this->getUser()->getID();
        $user_data = $em->getRepository('CatalystUserBundle:User')->find($id);
        $warehouse_id = $user_data->getWarehouse();

        $warehouse = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($warehouse_id);
        $params['warehouse'] = array(
                'name' => $warehouse->getName(),
                'pm_terms' => $warehouse->getPaymentTerm()
            );

        // product
        $params['prod_opts'] = $inv->getProductOptions();
        return $params;
    }


    protected function hookPostSave($obj,$is_new = false) 
    {
        $em = $this->getDoctrine()->getManager();
        if($is_new){
            $obj->setUserCreate($this->getUser());
            $obj->generateCode();
            $em->persist($obj);
            $em->flush();
        }
       
    }
    
   public function statusApproveAction($id)
    {
        return $this->statusUpdate($id, RequestOrder::STATUS_APPROVED);
    }

    public function statusSendAction($id)
    {
        return $this->statusUpdate($id, RequestOrder::STATUS_SENT);
    }

    public function statusCancelAction($id)
    {
        return $this->statusUpdate($id, RequestOrder::STATUS_CANCEL);
    }

    public function statusFulfillAction($id)
    {
        return $this->statusUpdate($id, RequestOrder::STATUS_FULFILLED);
    }
    
    protected function statusUpdate($id, $status)
    {
        $em = $this->getDoctrine()->getManager();
        $log = $this->get('catalyst_log');
        $req = $em->getRepository('SereniteaOrderBundle:RequestOrder')->find($id);
        if($req == null)
            throw new ValidationException("Cannot find request.");

        $req->setStatus($status);
        $em->flush();
        
        $log->log('ser_pur_pr_status_update', 'status updated for Requested Item ' . $req->getID() . '.', $req->toData());

        $this->addFlash('success', 'Request Order ' . $req->getCode() . ' status has been updated to ' . $status . '.');

        return $this->redirect($this->generateUrl('ser_pur_pr_edit_form', array('id' => $id)));
    }
}