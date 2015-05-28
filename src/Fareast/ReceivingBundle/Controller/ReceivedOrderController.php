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

    public function receivedAction($pr_id)
    {
        return $this->render('FareastReceivingBundle:ReceivedOrder:receive.html.twig');
    }
}
