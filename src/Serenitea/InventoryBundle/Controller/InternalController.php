<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class InternalController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'serenitea_inv';
        $this->title = 'Commissary Inventory';

        $this->list_title = 'Commissary Inventory';
        $this->list_type = 'dynamic';
    }
    
    public function indexAction()
    {
        $this->title = 'Commissary Inventory';
        $params = $this->getViewParams('', 'serenitea_inv_index');
        
        $gl = $this->setupGridLoader();

        $this->print = 'serenitea_inv_print';
        $this->csv = 'serenitea_inv_csv';
        
        $params['title'] = $this->title;
        $params['print'] = $this->print;
        $params['csv'] = $this->csv;
        $params['grid_cols'] = $gl->getColumns();
        
        return $this->render('SereniteaInventoryBundle:Internal:index.html.twig', $params);
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Item Code', '', ''),
            $grid->newColumn('Description', '', ''),
            $grid->newColumn('Specs', '', ''),
            $grid->newColumn('Quantity', '', ''),
            $grid->newColumn('Product Category', '', ''),            
        );
    } 
    
    public function headers()
    {
        // csv headers
        $headers = [
            'Item Code',
            'Item Name',            
            'Specs',
            'Quantity',
            'Product Category',
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
        $params = $this->getViewParams('', 'serenitea_inv_index');

//        $params['grid_cols'] = $this->headers();
//        $params['data'] = $data;

        return $this->render(
            'SereniteaInventoryBundle:Internal:print.html.twig', $params);
    }

    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }
    
    public function editAction()
    {
        $this->title = 'Internal Inventory';
        $params = $this->getViewParams('', 'serenitea_inv_edit');
        return $this->render('SereniteaInventoryBundle:Internal:edit.html.twig', $params);
    }

}
