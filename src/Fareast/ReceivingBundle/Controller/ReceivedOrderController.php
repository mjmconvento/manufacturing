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
            $grid->newColumn('Date Requested', 'getDateDeliver', 'date_deliver'),                    
            $grid->newColumn('Requested By', 'getUserCreate', 'user_create'),            
            $grid->newColumn('Status','getStatus','status_id')  
        );
    }

    public function padFormParams(&$params, $po=null)
    {
        $pur = $this->get('catalyst_purchasing');
        $request_opts = array(0 => '[Select PR]');
        $params['request_opts'] = $request_opts + $pur->getRequestOptions();
    }

    public function getPurchaseRequestAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $em->getRepository('CatalystPurchasingBundle:PurchaseRequest')->findAll();

        $data = array();
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
        $em = $this->getDoctrine()->getManager();

        $params = $this->getViewParams('', 'feac_receiving_index');
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

        return $this->render('FareastReceivingBundle:ReceivedOrder:received.html.twig', $params);
    }

    protected function update($o, $data, $is_new = false)
    {
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die();

        $pur = $this->get('catalyst_purchasing');
        $po_id = $data['request_id'];
        
        $po = $pur->getPurchaseRequest($po_id);
        $o->setPurchaseRequest($po);
        $o->setCode($data['dr_code']);
        $o->setDateDeliver(new DateTime($data['date_deliver']));

        if($is_new)
        {
            $this->updateTrackCreate($o, $data, $is_new);
            // clear all entries
            $pur->clearDeliveryEntries($o);

            foreach($data('delivery_qty') as $prod_id => $items)
            {
                $parentProd = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                foreach($items as $index => $item)
                {
                    $qty = $item;

                    $prdelivery = new RODeliveryEntry();
                    $prdelivery->setQuantity($qty)
                                ->setProduct($parentProd);

                    $o->addEntry($prdelivery);                    
                }
            }
        }

        $em->persist($o);
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
            return $this->redirect($this->generateUrl('feac_receiving_index', array('id' => $id)));
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
