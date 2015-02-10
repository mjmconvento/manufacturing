<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
    
 
    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List', 'ser_receive_index');

        $twig_file = 'SereniteaOrderBundle:Receive:index.html.twig';

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

        $this->title = 'Receiving Items';
        $params = $this->getViewParams('', 'ser_receive_index');

//        $params['grid_cols'] = $this->headers();
//        $params['data'] = $data;

        return $this->render(
            'SereniteaOrderBundle:Request:print.html.twig', $params);
    }
    
    public function editAction(){
        $this->title = 'Incomplete Deliveries';
        $params = $this->getViewParams('', 'ser_receive_edit');

        return $this->render('SereniteaOrderBundle:Request:edit.html.twig', $params);
    }
    
    protected function newBaseClass() {
        
    }
    protected function getObjectLabel($obj)
    {
//        if ($obj == null) {
//            return '';
//        }
//        return $obj->getCode();
    }
    
//    protected function getGridColumns()
//    {
//        $grid = $this->get('catalyst_grid');
//        return array(
//            $grid->newColumn('PO #', 'getCode', 'code'),
//            $grid->newColumn('Date Issued', 'getDateIssue', 'date_issue', 'o', array($this, 'formatDate')),
//            $grid->newColumn('Supplier', 'getName', 'name', 's'),
//            $grid->newColumn('Price', 'getTotalPrice', 'total_price'),
//            $grid->newColumn('Status', 'getStatusFormatted', 'status_id'),
//        );
//    }
    
   
    

}
