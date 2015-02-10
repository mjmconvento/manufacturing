<?php

namespace Serenitea\DashboardBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController as Controller;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class BelowController extends Controller
{
	public function indexAction()
	{
		$this->title = 'Product Below Threshold';
		$params = $this->getViewParams('', 'serenitea_dashboard_below');
		$this->csv = 'serenitea_dashboard_below_csv';
		
        $inv = $this->get('catalyst_inventory');

        $sample = $this->getUser()->getGroups();
        
        $params['br_opts'] = $inv->getBranchOptions();
        $params['sample'] = $sample;
        $params['csv'] = $this->csv;
        $params['title'] = $this->title;

		return $this->render('SereniteaDashboardBundle:Dashboard:below.html.twig', $params);
	}

	public function headers()
    {
        // csv headers
        $headers = [
            'Item Code',
            'Description',            
            'Quantity',
            'Minimum Threshold',                   
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