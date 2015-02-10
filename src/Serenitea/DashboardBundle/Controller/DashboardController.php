<?php

namespace Serenitea\DashboardBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController as Controller;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class DashboardController extends Controller
{
    public function indexAction()
    {
    	$this->title = 'Dashboard';
        $params = $this->getViewParams('', 'ser_dashboard_index');
        $sample = $this->getUser()->getGroups();
        
        $params['sample'] = $sample;


        return $this->render('SereniteaDashboardBundle:Dashboard:index.html.twig', $params);
    }

    public function unfulfilledAction()
    {
    	$this->title = 'Unfulfilled Request';
        $params = $this->getViewParams('', 'serenitea_dashboard_unfulfilled');
        $this->csv = 'serenitea_dashboard_unfulfilled_csv';
        $params['csv'] = $this->csv;
        $params['title'] = $this->title;
        
        return $this->render('SereniteaDashboardBundle:Dashboard:unfulfilled.html.twig', $params);
    }

    public function headers()
    {
        // csv headers
        $headers = [
            'P.O. #',
            'Date Issued',            
            'Target Delivery Date',        
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
}
