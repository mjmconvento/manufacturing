<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class CompleteController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'ser_complete';
        $this->title = 'Completed Delivery';

        $this->list_title = 'Completed Deliveries';
        $this->list_type = 'dynamic';
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

        $params = $this->getViewParams('List', 'ser_complete_index');

        $twig_file = 'SereniteaOrderBundle:Complete:form.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();        

        $sample = $this->getUser();
        
        $params['sample'] = $sample;
                            
        $this->print = 'ser_complete_print';
        $this->csv = 'ser_complete_csv';
        
        $params['title'] = $this->title;
        $params['print'] = $this->print;
        $params['csv'] = $this->csv;

        return $this->render($twig_file, $params); 
    }
    
    public function headers()
    {
        // csv headers
        $headers = [
            'P.O. #',
            'D.R. #',            
            'Status',
        ];
        return $headers;
    }
    
    public function csvAction(){

        // filename generate
        $date = new DateTime();
        $filename = $date->format('Ymdis') . '.csv';

        // redirect file to stdout, store in output buffer and place in $csv
        $file = fopen('php://output', 'w');
        ob_start();

        $csv_headers = $this->headers();

        fputcsv($file, $csv_headers);

        fclose($file);


        // csv header
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);

        return $response;
    }       
    
    public function printAction()
    {       

        // fetch data
//        $data = $this->fetchData($date_from, $date_to);

        $this->title = 'Completed Deliveries';
        $params = $this->getViewParams('', 'ser_complete_index');

//        $params['grid_cols'] = $this->headers();
//        $params['data'] = $data;

        return $this->render(
            'SereniteaOrderBundle:Complete:print.html.twig', $params);
    }
    
    public function viewAction(){
        $this->title = 'Completed Deliveries';
        $params = $this->getViewParams('', 'ser_complete_view');
        
        return $this->render('SereniteaOrderBundle:Complete:view.html.twig', $params);
    }
    
    protected function newBaseClass() 
    {
        
    }
    protected function getObjectLabel($obj)
    {

    }
}
