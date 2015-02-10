<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\UserBundle\Entity\BranchManagement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class BranchController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'serenitea_inv_br';
        $this->title = 'Branch Inventory';

        $this->list_title = 'Branch Inventory';
        $this->list_type = 'static';
    }
    
    public function indexAction()
    {
        $this->title = 'Branch Inventory';
        $params = $this->getViewParams('', 'serenitea_inv_br_index');
        $inv = $this->get('catalyst_inventory');
        $sample = $this->getUser()->getGroups();

        $params['sample'] = $sample;
        
        $this->print = 'serenitea_inv_br_print';
        $this->csv = 'serenitea_inv_br_csv';
        
        $params['br_opts'] = $inv->getBranchOptions();
        $params['title'] = $this->title;
        $params['print'] = $this->print;
        $params['csv'] = $this->csv;
        
        return $this->render('SereniteaInventoryBundle:Branch:index.html.twig', $params);
    }
    
    public function headers()
    {
        // csv headers
        $headers = [
            'Branch',
            'Item Code',            
            'Product Name',
            'Quantity',            
            'Expiration Date',
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

        $this->title = 'Commissary Inventory';
        $params = $this->getViewParams('', 'serenitea_inv_br_index');

//        $params['grid_cols'] = $this->headers();
//        $params['data'] = $data;

        return $this->render(
            'SereniteaInventoryBundle:Internal:print.html.twig', $params);
    }

    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }

}
