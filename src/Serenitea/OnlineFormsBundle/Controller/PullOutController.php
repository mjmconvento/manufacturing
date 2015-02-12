<?php

namespace Serenitea\OnlineFormsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use DateTime;

class PullOutController extends CrudController
{
     public function __construct()
    {
        $this->route_prefix = 'ser_pullout';
        $this->title = 'Pull Out Form';

        $this->list_title = 'Pull Out Forms';
        $this->list_type = 'dynamic';
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Pull Out No.','',''),
            $grid->newColumn('Date Created','',''),
            $grid->newColumn('Created By','',''),
            $grid->newColumn('Status','',''),
        );
    }

    protected function getObjectLabel($obj)
    {
        
    }

    protected function newBaseClass()
    {

    }
    
    // public function headers()
    // {
    //     // csv headers
    //     $headers = [
    //         'Pull Out #',
    //         'Date Created',            
    //         'Created By',            
    //     ];
    //     return $headers;
    // }
    
    // public function csvAction(){

    //     // filename generate
    //     $date = new DateTime();
    //     $filename = $date->format('Ymdis') . '.csv';

    //     // redirect file to stdout, store in output buffer and place in $csv
    //     $file = fopen('php://output', 'w');
    //     ob_start();

    //     $csv_headers = $this->headers();

    //     fputcsv($file, $csv_headers);

    //     fclose($file);


    //     // csv header
    //     $response = new Response();
    //     $response->headers->set('Content-Type', 'text/csv');
    //     $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);

    //     return $response;
    // }      
}
