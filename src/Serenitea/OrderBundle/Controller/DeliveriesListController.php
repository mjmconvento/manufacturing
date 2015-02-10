<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use DateTime;

class DeliveriesListController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'serenitea_dlist';
        $this->title = 'Delivery List';

        $this->list_title = 'Deliveries List';
        $this->list_type = 'static';
    }
    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('', 'serenitea_dlist_index');

        $twig_file = 'SereniteaOrderBundle:Requested:deliver.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();
      
        $inv = $this->get('catalyst_inventory');
        
        $date_from = new DateTime();
        $date_to = new DateTime();
        $date_from->format("Y-m-d");
        $date_to->format("Y-m-d");
        
        $this->padFormParams($params, $date_from, $date_to);

        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;
        $params['br_opts'] = $inv->getBranchOptions();
        
        return $this->render($twig_file, $params);
    }
    
    public function viewAction()
    {
        $this->title = 'Deliveries List';
        $params = $this->getViewParams('', 'serenitea_dlist_view');

        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        
        $prods = $em->getRepository('CatalystInventoryBundle:Product')->findAll();
        $prod_opts = array();
        foreach ($prods as $prod)
            $prod_opts[$prod->getID()] = $prod->getName();
        $params['prod_opts'] = $prod_opts;

        $sample = $this->getUser()->getGroups();
        
        $params['sample'] = $sample;

        $params['status_opts'] = array(
            'draft' => 'Draft',
            'approved' => 'Approved',
            'fulfilled' => 'Fulfilled',
            'sent' => 'Sent',
            'cancelled' => 'Cancel',
        );

        return $this->render('SereniteaOrderBundle:Requested:receipt.html.twig', $params);
    }
    
//    public function editAction(){
//        $this->title = 'Requested Order';
//        $params = $this->getViewParams('', 'serenitea_rlist_edit');
//        return $this->render('SereniteaInventoryOrderBundle:Request:edit.html.twig', $params);
//    }

    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }

}