<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController;
use DateTime;

class RequestedController extends BaseController
{
    public function __construct()
    {
        $this->route_prefix = 'ser_rlist_index';
    }

    public function indexAction()
    {
        $this->title = 'Requested Items List';

        $params = $this->getViewParams('',$this->route_prefix);
            
        $em = $this->getDoctrine()->getManager();

        //show all data with sent status
        $query = $em->createQuery('SELECT u FROM Serenitea\OrderBundle\Entity\RequestOrder u WHERE u.status_id = :status');
        $query->setParameter('status','Sent');
        $request = $query->getResult();
        $params['request'] = $request;
        
        return $this->render('SereniteaOrderBundle:Requested:form.html.twig', $params);
    }
}