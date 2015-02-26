<?php

namespace Catalyst\ReportBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class DamagedController extends CrudController{
    
    public function __construct()
    {
        $this->route_prefix = 'catalyst_report_damaged';
        $this->title = 'Damaged Report';

        $this->list_title = 'Damaged Report';
        $this->list_type = 'static';
    }
    
    public function indexAction() {
        $this->title = 'Damaged Report';
        $params = $this->getViewParams('', 'catalyst_report_damaged_summary');
        $inv = $this->get('catalyst_inventory');
        
        $this->print = 'catalyst_report_damaged_print';
        $this->csv = 'catalyst_report_damaged_csv';        
        
        $date_from = new DateTime();
        $date_to = new DateTime();
        $date_from->format("Y-m-d");
        $date_to->format("Y-m-d");

        $this->padFormParams($params, $date_from, $date_to);

        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;
        $params['title'] = $this->title;
        $params['print'] = $this->print;
        $params['csv'] = $this->csv;
        $params['br_opts'] = $inv->getBranchOptions();
        $params['prod_opts'] = $inv->getProductGroupOptions();
        
        
        return $this->render('CatalystReportBundle:Damaged:index.html.twig', $params);
    }
    
    public function headers()
    {
        // csv headers
        $headers = [
            'Item Code',
            'Description',            
            'Specs',
            'Damage Qty',
            'Total Price',                       
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

        $this->title = 'Damaged Item Report';
        $params = $this->getViewParams('', 'catalyst_report_damaged_summary');

//        $params['grid_cols'] = $this->headers();
//        $params['data'] = $data;

        return $this->render('CatalystReportBundle:Damaged:print.html.twig', $params);
    }

    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }

}
