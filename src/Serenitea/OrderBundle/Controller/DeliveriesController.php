<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use DateTime;

class DeliveriesController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'ser_dlist';
        $this->title = 'Delivery List';

        $this->list_title = 'Deliveries List';
        $this->list_type = 'dynamic';
    }
    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('', 'ser_dlist_index');

        $twig_file = 'SereniteaOrderBundle:Delivery:form.html.twig';

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
        // $params['br_opts'] = $inv->getBranchOptions();
        
        return $this->render($twig_file, $params);
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('D.R. #', '', ''),
            $grid->newColumn('Date Created', '', ''),
            $grid->newColumn('Delivery Date', '', ''),
            $grid->newColumn('Branch', '', ''),
            $grid->newColumn('Status', '', ''),
        );
    }
    
    public function viewAction()
    {
       $this->title = 'Deliveries List';
       $params = $this->getViewParams('', 'ser_dlist_view');
       return $this->render('SereniteaOrderBundle:Delivery:view.html.twig', $params);
    }

    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }

}