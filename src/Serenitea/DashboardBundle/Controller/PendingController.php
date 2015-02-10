<?php

namespace Serenitea\DashboardBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use DateTime;

class PendingController extends CrudController
{   
    public function pendingAction()
    {
        $this->title = 'Pending Deliveries';
        $params = $this->getViewParams('', 'serenitea_dashboard_pending');
        $this->csv = 'serenitea_dashboard_pending_csv';
        
        $date_from = new DateTime();
        $date_to = new DateTime();
        $date_from->format("Y-m-d");
        $date_to->format("Y-m-d");
        
        $this->padFormParams($params, $date_from, $date_to);

        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;        
        $params['csv'] = $this->csv;
        $params['title'] = $this->title;
        
        
        return $this->render('SereniteaDashboardBundle:Dashboard:pending.html.twig', $params);
    }
        
//    public function editAction(){
//        $this->title = 'Requested Order';
//        $params = $this->getViewParams('', 'serenitea_rlist_edit');
//        return $this->render('SereniteaInventoryOrderBundle:Request:edit.html.twig', $params);
//    }

    public function headers()
    {
        // csv headers
        $headers = [
            'D.R. Receipt #',
            'Date Created',            
            'Delivery Date',
            'Branch',
            'Items',
            'Quantity',
            'Amount',
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

    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }

}