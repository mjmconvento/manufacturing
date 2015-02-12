<?php

namespace Serenitea\OnlineFormsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use DateTime;

class RepairController extends CrudController
{
     public function __construct()
    {
        $this->route_prefix = 'ser_repair';
        $this->title = 'Job Order Form';

        $this->list_title = 'Job Order Forms';
        $this->list_type = 'dynamic';
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Job Order No.','',''),
            $grid->newColumn('Date Created','',''),
            $grid->newColumn('Created By','',''),
            $grid->newColumn('Status','',''),
        );
    }
    
 
    // public function indexAction()
    // {
    //     $this->title = 'Job Order Forms';
    //     $params = $this->getViewParams('', 'serenitea_repair_index');
        
    //     $this->csv = 'serenitea_repair_csv';
        
    //     $params['csv'] = $this->csv;
        
    //     return $this->render('SereniteaOnlineFormsBundle:Repair:index.html.twig', $params);
    // }

    public function headers()
    {
        // csv headers
        $headers = [
            'Job Order #',
            'Date Created',            
            'Created By',            
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

    protected function getObjectLabel($obj) 
    {
        
    }

    protected function newBaseClass() 
    {
        
    }
    

}
