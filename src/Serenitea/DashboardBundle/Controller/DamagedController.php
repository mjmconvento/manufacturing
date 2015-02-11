<?php

namespace Serenitea\DashboardBundle\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Catalyst\TemplateBundle\Model\BaseController as Controller;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class DamagedController extends Controller
{
	public function damageAction()
	{
		$this->title = 'Damaged Items';
        $params = $this->getViewParams('', 'serenitea_dashboard_damaged');
        $this->csv = 'serenitea_dashboard_damaged_csv';
        $inv = $this->get('catalyst_inventory');

        $sample = $this->getUser()->getGroups();
        
        $params['br_opts'] = $inv->getBranchOptions();
        $params['sample'] = $sample;
        $params['csv'] = $this->csv;
        $params['title'] = $this->title;
        
        return $this->render('SereniteaDashboardBundle:Dashboard:damaged.html.twig', $params);
	}

	public function headers()
    {
        // csv headers
        $headers = [
            'Branch',
            'Item Code',            
            'Item Name',
            'Quantity',                
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