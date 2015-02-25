<?php

namespace Catalyst\PurchasingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
use Catalyst\PurchasingBundle\Entity\POEntry;
use Catalyst\PurchasingBundle\Entity\PODelivery;
use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\InventoryBundle\Entity\ProductAttribute;


use Catalyst\ValidationException;
use DateTime;

class DeliveryController extends CrudController
{
    use TrackCreate;
    public function __construct() {
        $this->title = 'Deliveries';
        $this->list_title = 'Deliveries';
        $this->list_type = 'static';
        $this->route_prefix = 'cat_pur_del';
        $this->repo = 'CatalystPurchasingBundle:PODelivery';
        
    }
    
    protected function newBaseClass()
    {
        return new PODelivery();
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
        $this->title = 'Purchase Order';
        $pur = $this->get('catalyst_purchasing');
        $params = $this->getViewParams('Deliveries', 'cat_pur_del_index');

        $params['object'] = $pur->getPurchaseOrder($id);

        return $this->render('CatalystPurchasingBundle:Delivery:index.html.twig', $params);
    }
    

    protected function findDelivery($id)
    {
        $em = $this->getDoctrine()->getManager();

        $deli = $em->getRepository('CatalystPurchasingBundle:PODelivery')->find($id);
        if ($deli == null)
            throw new ValidationException('Cannot find purchase order delivery.');

        return $deli;
    }
    
    protected function padFormParams(&$params, $object = null) {
        $po_id = $this->getRequest()->get('po_id');
        if($po_id != '')
        {
            $pur = $this->get('catalyst_purchasing');
            $po = $pur->getPurchaseOrder($po_id);
            $object->setPurchaseOrder($po);
        }
        if($object == null){
            $params['is_new'] = true;
        }else {
             $params['is_new'] = false;
        }
    }

    protected function update($delivery, $data, $is_new = true)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        $pur = $this->get('catalyst_purchasing');
        $po_id = $this->getRequest()->get('po_id');
        if($po_id != '')
        {
            $po = $pur->getPurchaseOrder($po_id);
            $delivery->setPurchaseOrder($po);
        }
        $delivery->setDateDeliver(new DateTime($data['date_deliver']));
        $delivery->setExternalCode($data['external_code']);
        $delivery->setCode($data['code']);
        if ($is_new)
        {
            $this->updateTrackCreate($delivery, $data, $is_new);
            // clear all entries
            $pur->clearDeliveryEntries($delivery);

            // add entries
            foreach ($data['delivery_qty'] as $prod_id => $items)
            {
                $parentProd = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                foreach($items as $index => $item){
                    $expiry = '';
                    if(isset($data['delivery_expiry'][$prod_id][$index])){
                        $expiry =  $data['delivery_expiry'][$prod_id][$index];
                    }
                    $qty = $item;
                    
                    $prodDelivery = $pur->findProductWithExpiry($parentProd,$expiry);
                    
                    $em->persist($prodDelivery);
                    $em->flush();
                    
                    $pode = new PODeliveryEntry();
                    $pode->setQuantity($qty)
                        ->setProduct($prodDelivery);

                    $delivery->addEntry($pode);
                }
                
            }
        }

        $em->persist($delivery);
        $em->flush();
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $delivery = $this->findDelivery($id);
        $po_id = $delivery->getPurchaseOrder()->getID();

        $em->remove($delivery);
        $em->flush();

        $this->addFlash('success', 'Delivery successfuly deleted.');

        return $this->redirect($this->generateUrl('cat_pur_po_edit_form', array('id' => $po_id)));
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
