<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\InventoryBundle\Entity\Warehouse;
use Catalyst\GridBundle\Model\FilterGroup;
use Catalyst\GridBundle\Model\Grid\Column as GCol;
use Catalyst\GridBundle\Model\Grid\Loader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WarehouseStockController extends BaseController
{
    public function indexAction()
    {
        $this->title = 'Warehouse Stock';

        $params = $this->getViewParams('','feac_inv_warehouse_index');

        $params['grid_cols'] = $this->getGridColumns();        

        $inv = $this->get('catalyst_inventory');        
        $params['dept'] = $inv->getInventoryAccountWarehouseOptions();
        $params['prodgroup_opts'] = $inv->getProductGroupOptions();
        
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die(); 

        return $this->render('FareastInventoryBundle:WarehouseStock:index.html.twig', $params);
    }

    protected function getGridColumns()
    {
        return array(
            new GCol('Warehouse', 'getName','name','w'),
            new GCol('Item Code', 'getSKU', 'sku','p'),            
            new GCol('Item Name', 'getName', 'name','p'),
            new GCol('Quantity', 'getQuantity', 'qty'),
            new GCol('Unit of Measure', 'getUnitOfMeasure', 'uom','p'),
        );
    }

    protected function setupGrid()
    {
        $grid = $this->get('catalyst_grid');
        $data = $this->getRequest()->query->all();
        $em = $this->getDoctrine()->getManager();

        // setup grid
        $gl = $grid->newLoader();
        $gl->processParams($data)
            ->setRepository('CatalystInventoryBundle:Stock')
            ->addJoin($grid->newJoin('p', 'product', 'getProduct'))
            ->addJoin($grid->newJoin('w', 'inv_account', 'getInventoryAccount'))
            ->enableCountFilter();

        // columns
        $stock_cols = $this->getGridColumns();
        foreach ($stock_cols as $col)
            $gl->addColumn($col);

        return $gl;
    }    

    public function gridAction($warehouse = null, $category = null)
    {
        $gl = $this->setupGrid();
        // $qry = array();

        $grid = $this->get('catalyst_grid');
        $fg = $grid->newFilterGroup();

        //filter warehouses
        if($warehouse != null and $warehouse != 'null')
        {
            $wh_id = explode(',',$warehouse);
            $fg->andWhere('w.id IN (:wh_id)')
                ->setParameter('wh_id', $wh_id);
        }

        //filter category
        if($category != null and $category != 'null')
        {
            $cat_id = explode(',',$category);
            $fg->andWhere('p.prodgroup IN (:cat_id)')
                ->setParameter('cat_id', $cat_id);
        }

        $fg->andWhere('o.quantity >= 0');

        // if(!empty($qry)){
        //     $filter = implode(' AND ', $qry);
        //     $fg->where($filter);
        //     $gl->setQBFilterGroup($fg);
        // }

        $gl->setQBFilterGroup($fg);
        $gres = $gl->load4();
        $resp = new Response($gres->getJSON());
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }

    public function exportCSVAction($warehouse = null, $category = null)
    {
        $filename = 'wh_stock_report.csv';
        $data = $this->getStockReport($warehouse, $category);

        // redirect file to stdout, store in output buffer and place in $csv
        $file = fopen('php://output', 'w');
        ob_start();

        fputcsv($file, $this->headers());

        foreach ($data as $dt)
            fputcsv($file, $dt);
        fclose($file);
        $csv = ob_get_contents();
        ob_end_clean();


        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->setContent($csv);

        return $response;
    }

    public function headers()
    {
        // csv headers
        $headers = [
            'Warehouse',
            'Item Code',            
            'Item Name',
            'Quantity',
            'Unit',
        ];
        return $headers;
    }

    public function getStockReport($warehouse = null, $category = null)
    {
        $em = $this->getDoctrine()->getManager();
        if($warehouse != null and $warehouse != 'null')
        {
            $query = $em->createQuery('SELECT w.id,p.sku, p.name, o.quantity, p.uom 
                FROM CatalystInventoryBundle:Stock o 
                INNER JOIN o.product p 
                INNER JOIN o.inv_account w
                WHERE w.id = :invaccount and o.quantity >= 0')                    
                    ->setParameter('invaccount', $warehouse);
        }
        else
        {
            $query = $em->createQuery('SELECT w.id, p.sku,  p.name, o.quantity, p.uom 
                FROM CatalystInventoryBundle:Stock o 
                INNER JOIN o.product p 
                INNER JOIN o.inv_account w 
                WHERE o.quantity >= 0');
        }

        if($category != null and $category != 'null')
        {
            $query = $em->createQuery('SELECT w.id ,p.sku, p.name, o.quantity, p.uom 
                FROM CatalystInventoryBundle:Stock o 
                INNER JOIN o.product p 
                INNER JOIN o.inv_account w
                WHERE p.prodgroup = :prod_group and o.quantity >= 0')
                    ->setParameter('prod_group', $category);                    
        }
        return $query->getResult();
    }

    public function printAction($warehouse = null, $category = null)
    {
        $this->title = 'Warehouse Stock';
        $params = $this->getViewParams('', 'feac_inv_warehouse_index');

        $params['grid_cols'] = $this->getGridColumns();
        $params['data'] = $this->getStockReport($warehouse, $category);

        return $this->render(
            'FareastInventoryBundle:WarehouseStock:print.html.twig', $params);
    }
}