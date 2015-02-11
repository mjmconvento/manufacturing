<?php

namespace Serenitea\PurchasingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use DateTime;

class CompleteController extends CrudController
{
	public function __construct()
	{
		$this->route_prefix = 'cat_pur_com';
		$this->title = 'Completed P.O.';

		$this->list_title = 'Completed P.O.';
		$this->list_type = 'static';
	}

	public function indexAction()
	{
		$this->title = 'Completed P.O.';
		$params = $this->getViewParams('List', 'cat_pur_com_index');

		$this->print = 'cat_pur_com_print';
        $this->csv = 'cat_pur_com_csv';
        
        $params['title'] = $this->title;
        $params['print'] = $this->print;
        $params['csv'] = $this->csv;

		return $this->render('SereniteaPurchasingBundle:Complete:index.html.twig', $params);
	}

	protected function newBaseClass()
	{

	}

	protected function getObjectLabel($obj)
	{

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

        $this->title = 'Completed POs';
        $params = $this->getViewParams('', 'cat_pur_com_index');

//        $params['grid_cols'] = $this->headers();
//        $params['data'] = $data;

        return $this->render('SereniteaPurchasingBundle:Complete:print.html.twig', $params);
    }

    public function viewAction()
    {
        $this->title = 'Completed P.O.';
        $params = $this->getViewParams('', 'cat_pur_com_view');

        return $this->render('SereniteaPurchasingBundle:Complete:view.html.twig', $params);
    }
}