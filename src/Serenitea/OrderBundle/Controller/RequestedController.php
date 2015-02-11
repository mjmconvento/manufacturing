<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use DateTime;

class RequestedController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'ser_rlist';
        $this->title = 'Requested Item List';

        $this->list_title = 'Requested Items Lists';
        $this->list_type = 'dynamic';
    }
    
    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('P.O. #','',''),
            $grid->newColumn('Date Created','',''),
            $grid->newColumn('Target Delivery Date','',''),
            $grid->newColumn('Branch','',''),
            $grid->newColumn('Status','',''),
        );
    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('', 'ser_rlist_index');

        $twig_file = 'SereniteaOrderBundle:Requested:form.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();                  
        
        $this->print = 'serenitea_rlist_print';
        $this->csv = 'serenitea_rlist_csv';
        $inv = $this->get('catalyst_inventory');

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
        // $params['br_opts'] = $inv->getBranchOptions();
        
        return $this->render($twig_file, $params);
    }
    
    public function headers()
    {
        // csv headers
        $headers = [
            'P.O. Receipt #',
            'Date Created',            
            'Target Delivery Date',
            'Branch',
            'SKU',
            'Qty',
            'Amt. Value',
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

        $this->title = 'Requested Items Lists';
        $params = $this->getViewParams('', 'serenitea_rlist_index');

//        $params['grid_cols'] = $this->headers();
//        $params['data'] = $data;

        return $this->render(
            'SereniteaOrderBundle:Request:print.html.twig', $params);
    }
    
    public function editAction()
    {        
        $params = $this->getViewParams('', 'ser_rlist_edit');

        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        
        // $prods = $em->getRepository('CatalystInventoryBundle:Product')->findAll();
        // $prod_opts = array();
        // foreach ($prods as $prod)
        //     $prod_opts[$prod->getID()] = $prod->getName();
        // $params['prod_opts'] = $prod_opts;

        // $sample = $this->getUser()->getGroups();
        
        // $params['sample'] = $sample;
        
        // $params['status_opts'] = array(
        //     'draft' => 'Draft',
        //     'approved' => 'Approved',
        //     'fulfilled' => 'Fulfilled',
        //     'sent' => 'Sent',
        //     'cancelled' => 'Cancel',
        // );
        
        return $this->render('SereniteaOrderBundle:Requested:edit.html.twig', $params);
    }

    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }

}
