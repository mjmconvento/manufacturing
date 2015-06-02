<?php

namespace Fareast\ReceivingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Fareast\ReceivingBundle\Entity\ReceivedOrder;
use Fareast\ReceivingBundle\Entity\RODeliveryEntry;
use Catalyst\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class ReceivedOrderController extends CrudController
{
    use TrackCreate;

    public function __construct()
    {
        $this->title = 'Receiving Order';
        $this->list_title = 'Receiving Orders';
        $this->list_type = 'dynamic';
        $this->route_prefix = 'feac_receiving';
    }

    protected function newBaseClass()
    {
        return new ReceivedOrder;
    }

    protected function getObjectLabel($obj)
    {
        if($obj == null)
        return '';
        return $obj->getCode();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Code', 'getCode', 'code'),
            $grid->newColumn('Date Requested', 'getDateDeliver', 'date_deliver','o',array($this, 'formatDate')),                    
            $grid->newColumn('Requested By', 'getName', 'request_by', 'u'),            
            $grid->newColumn('Status','getStatus','status_id')  
        );
    }

    public function formatDate($date)
    {
        return $date->format('m/d/Y');
    }

    protected function getGridJoins()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newJoin('u', 'user_create', 'getUserCreate', 'left'),
        );
    }

    public function padFormParams(&$params, $object=null)
    {
        $pur = $this->get('catalyst_purchasing');
        $request_opts = array(0 => '[Select PR]');
        $params['request_opts'] = $request_opts + $pur->getRequestOptions();
    }

    public function getPurchaseRequestAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $em->getRepository('CatalystPurchasingBundle:PurchaseRequest')->findAll();

        $received = $em->getRepository('FareastReceivingBundle:ReceivedOrder')->findAll();

        $data = array();        
        foreach($received as $rcv)
        {
            if($rcv->getPurchaseRequest()->getID() == $id)
            {
                $data = [
                    'code' => $rcv->getCode(),
                    'date_create' => $rcv->getDateCreate()->format('m/d/Y'),
                    'id' => $rcv->getID(),
                ];
            }
        }

        foreach($request as $r)
        {
            if($r->getID() == $id)
            {
            $data = [
                'date_issue' => $r->getDateCreate()->format('m/d/Y'),
                'user_create' => $r->getUserCreate()->getName(),
            ];
            }
        }

        return new JsonResponse($data);
    }

    public function receivedFormAction($pr_id)
    {
        $this->checkAccess($this->route_prefix . '.add');

        $em = $this->getDoctrine()->getManager();

        $this->hookPreAction();
        $obj = $this->newBaseClass();

        $params['object'] = $obj;

        $params = $this->getViewParams('Add', 'feac_receiving_pr_received_form');
        $request = $em->getRepository('CatalystPurchasingBundle:PurchaseRequest')->findAll();
        $data = array();
        foreach($request as $r)
        {
            if($r->getID() == $pr_id)
            {
                $data = [
                    'code' => $r->getCode(),
                    'date_issue' => $r->getDateCreate()->format('m/d/Y'),
                    'user_create' => $r->getUserCreate()->getName(),
                    'entries' => $r->getEntries(),                    
                ];
            }
        }

        $params['data'] = $data;

        $this->padFormParams($params, $obj);

        return $this->render('FareastReceivingBundle:ReceivedOrder:received.html.twig', $params);
    }

    protected function update($ro, $data, $is_new = false)
    {
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die();
        $em = $this->getDoctrine()->getManager();
        $pur = $this->get('catalyst_purchasing');
        // $pr_id = $data['request_id'];

        $pr_id = $this->getRequest()->get('pr_id');        
        $ro = new ReceivedOrder();
        $pr = $pur->getPurchaseRequest($pr_id);
        $ro->setPurchaseRequest($pr);
        $ro->setCode($data['dr_code']);
        $ro->setDateDeliver(new DateTime($data['date_deliver']));

        if($is_new)
        {
            $this->updateTrackCreate($ro, $data, $is_new);
            // clear all entries
            $pur->clearDeliveryEntries($ro);
            $ro->generateCode();

            foreach($data['delivered_qty'] as $prod_id => $items)
            {
                $parentProd = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                foreach($items as $index => $item)
                {
                    $qty = $item;

                    $prdelivery = new RODeliveryEntry();
                    $prdelivery->setQuantity($qty)
                                ->setProduct($parentProd);

                    $ro->addEntry($prdelivery);                    
                }
            }
        }

        $em->persist($ro);
        $em->flush();

    }

    public function addSubmitAction()
    {
        $this->checkAccess($this->route_prefix . '.add');
        $id = $this->getRequest()->get('pr_id');
        $this->hookPreAction();
        try
        {
            $obj = $this->add();

            $this->addFlash('success', $this->title . ' added successfully.');
            return $this->redirect($this->generateUrl('feac_receiving_add_form', array('id' => $id)));
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
}
