<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Serenitea\OrderBundle\Entity\RequestOrder;
use Serenitea\OrderBundle\Entity\ROEntry;
use Serenitea\OrderBundle\Entity\RODelivery;
use Serenitea\OrderBundle\Entity\RODeliveryEntry;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\ValidationException;
use DateTime;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeliveryController extends CrudController
{
    use TrackCreate;
    public function __construct()
    {
        $this->route_prefix = 'ser_dlist';
        $this->title = 'Delivery List';
        $this->list_title = 'Deliveries List';
        $this->list_type = 'static';
        $this->repo = 'SereniteaOrderBundle:RODelivery';
    }

    protected function newBaseClass() 
    {
        return new RODelivery();
    }

    protected function getObjectLabel($obj) 
    {
        if ($obj == null)
            return '';
        return $obj->getCode();
    }

    public function indexAction()
    {
       $id = $this->getRequest()->get('id');
       $this->title = 'Requested Item';
       $pur = $this->get('catalyst_purchasing');
       $params = $this->getViewParams('Deliveries','ser_pur_del_index');

       $params['object'] = $pur->getRequestOrder($id);

       return $this->render('SereniteaOrderBundle:Delivery:index.html.twig', $params);
    }

    protected function padFormParams(&$params, $object = null) {
        $req_id = $this->getRequest()->get('req_id');
        if($req_id != '')
        {
            $pur = $this->get('catalyst_purchasing');
            $ro = $pur->getRequestOrder($req_id);
            $object->setRequestOrder($ro);
        }
        if($object == null){
            $params['is_new'] = true;
        }else {
             $params['is_new'] = false;
        }
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');                
        $params['prod_opts'] = $inv->getProductOptions();

    }

    protected function findDelivery($id)
    {
        $em = $this->getDoctrine()->getManager();

        $deli = $em->getRepository('SereniteaOrderBundle:RODelivery')->find($id);
        if ($deli == null)
            throw new ValidationException('Cannot find request order delivery.');

        return $deli;
    }

    protected function update($delivery, $data, $is_new = true)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        $pur = $this->get('catalyst_purchasing');

        $req_id = $this->getRequest()->get('req_id');
        if($req_id != '')
        {
            $ro = $pur->getRequestOrder($req_id);
            $delivery->setRequestOrder($ro);
        }

        $delivery->setDateDeliver(new DateTime($data['date_deliver']));
        $delivery->setExternalCode($data['external_code']);
        $delivery->setCode($data['code']);
    }

    public function checkAction($var_id)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT u FROM Catalyst\InventoryBundle\Entity\Product u JOIN u.parent p WHERE p.id = :id ');
        $query->setParameter('id', $var_id);
        $opt = $query->getResult();
        $so_data = [];
        foreach ($opt as $o)
        {
            $so_data[] = [
                'parent_id' => $o->getParent(),
                'sku' => $o->getSKU(),
            ];
        }

        return new JsonResponse($so_data);        
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $delivery = $this->findDelivery($id);
        $req_id = $delivery->getRequestOrder()->getID();

        $em->remove($delivery);
        $em->flush();

        $this->addFlash('success', 'Delivery successfuly deleted.');

        return $this->redirect($this->generateUrl('cat_pur_po_edit_form', array('id' => $req_id)));
    }   

    public function receiveAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        $pur = $this->get('catalyst_purchasing');
        $delivery = $pur->getDelivery($id);
        
        $trans = $inv->newTransaction();
        $trans->setDescription('Delivery for'.$delivery->getCode());
        $trans->setUserCreate($this->getUser());

        foreach ($delivery->getEntries() as $entry){
            $entries = $inv->itemsIn($entry->getProduct(),$entry->getQuantity());
        
            foreach ($entries as $ientry){
                $trans->addEntry($ientry);
            }
        }
        $inv->persistTransaction($trans);
        
        $delivery->setReceived();
        $em->persist($delivery);
        $em->flush();
        
        $this->addFlash('success', $delivery->getCode().' Received and locked.');
        return $this->redirect($this->generateUrl($this->getRouteGen()->getEdit(), array('id' => $id)));
    }

    public function searchAction(){
        $data = $this->getRequest()->request->all();
        $pur = $this->get('catalyst_purchasing');
        $params = $this->getViewParams('Deliveries', 'cat_pur_del_search');
        $params['list_title'] = $this->list_title;  
        $params['po_opts'] = $pur->findPurchaseOrderOptions(array('status_id' => 'Sent'));
        
        
        if(isset($data['po_code'])){
            $id = $data['po_code'];
            $po = $pur->getPurchaseOrder($id);
            return $this->redirect($this->generateUrl('cat_pur_del_index', array('id' => $po->getID())));
        }
        return $this->render('CatalystPurchasingBundle:Delivery:search.html.twig', $params);
    }

    public function addSubmitAction()
    {
        $this->checkAccess($this->route_prefix . '.add');
        $id = $this->getRequest()->get('po_id');
        $this->hookPreAction();
        try
        {
            $obj = $this->add();

            $this->addFlash('success', $this->title . ' added successfully.');
            return $this->redirect($this->generateUrl('cat_pur_del_index', array('id' => $id)));
        }
        catch (ValidationException $e)
        {
            $this->addFlash('error', $e->getMessage());
            return $this->addError($obj);
        }
        catch (DBALException $e)
        {
            print_r($e->getMessage());
            $this->addFlash('error', 'Database error encountered. Possible duplicate.');
            error_log($e->getMessage());
            return $this->addError($obj);
        }
    }
    
    protected function hookPostSave($obj, $is_new = false) {
        $em = $this->getDoctrine()->getManager();
        if($is_new){
            $obj->generateCode();
            $obj->setUserCreate($this->getUser());
            $em->persist($obj);
            $em->flush();
        }
    }

}