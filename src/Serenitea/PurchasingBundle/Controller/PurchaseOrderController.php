<?php

namespace Serenitea\PurchasingBundle\Controller;

use Catalyst\PurchasingBundle\Controller\PurchaseOrderController as Controller;


class PurchaseOrderController extends Controller
{
    public function poAction()
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

        return $this->render('SereniteaPurchasingBundle:PurchaseOrder:add.html.twig', $params);
    }
}
