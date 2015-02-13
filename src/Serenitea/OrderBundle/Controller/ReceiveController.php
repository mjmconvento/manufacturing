<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use DateTime;

class ReceiveController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'ser_receive';
        $this->title = 'Incomplete Delivery';

        $this->list_title = 'Incomplete Deliveries';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass() 
    {
        
    }
    protected function getObjectLabel($obj)
    {
        
    }  
    
    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('D.R. #','',''),            
            $grid->newColumn('Target Delivery Date','',''),            
            $grid->newColumn('Status','',''),
        );
    }
 
    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List', 'ser_receive_index');

        $twig_file = 'SereniteaOrderBundle:Incomplete:form.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();        

        $sample = $this->getUser();
        
        $params['sample'] = $sample;                
        
        $this->print = 'ser_receive_print';
        $this->csv = 'ser_receive_csv';
        
        $params['title'] = $this->title;
        $params['print'] = $this->print;
        $params['csv'] = $this->csv;

        return $this->render($twig_file, $params);      
    }
    
    public function viewAction(){
        $this->title = 'Incomplete Deliveries';
        $params = $this->getViewParams('', 'ser_receive_view');

        return $this->render('SereniteaOrderBundle:Incomplete:view.html.twig', $params);
    }
    

}
