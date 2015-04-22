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

class StockAdjustmentController extends BaseController
{
    public function indexAction()
    {
        $this->title = 'Stock Adjustment';

        $params = $this->getViewParams('','feac_inv_adjust_index');

        $params['grid_cols'] = $this->getGridColumns();
        $em = $this->getDoctrine()->getManager();

        $inv = $this->get('catalyst_inventory');
        $params['dept'] = $inv->getWarehouseOptions();
        $params['prodgroup_opts'] = $inv->getProductGroupOptions();        

        return $this->render('FareastInventoryBundle:StockAdjustment:index.html.twig', $params);
    }

    protected function getGridColumns()
    {
        return array(
            new GCol('Item Code', 'getSKU', 'sku', 'p'),
            new GCol('Item Name', 'getName', 'name', 'p'),
            new GCol('Quantity', 'getQuantity', 'qty'),
            new GCol('Unit of Measure', 'getUnitOfMeasure', 'uom', 'p'),
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
            // ->setQBFilterGroup($fg);

        // columns
        $stock_cols = $this->getGridColumns();
        foreach ($stock_cols as $col)
            $gl->addColumn($col);

        return $gl;
    }

    public function gridAction($dept = null, $category = null)
    {
        $gl = $this->setupGrid();
        $qry = array();
        
        
        $grid = $this->get('catalyst_grid');
        $fg = $grid->newFilterGroup();

        if($dept != null and $dept != 'null' ){
            $aw_id = explode(',',$dept);
            $qry[] = 'w.id IN ('. implode(', ', $aw_id).')';
        }

        if ($category != null and $category != 'null' )
        {
            $aw_id = explode(',',$category);
            $qry[] = 'p.prodgroup IN ('. implode(', ', $aw_id).')';
        }

        $qry[] = 'o.quantity > 0';
        
        if(!empty($qry)){
            $filter = implode(' AND ', $qry);
            $fg->where($filter);
            $gl->setQBFilterGroup($fg);
        }
        $gres = $gl->load();

        $resp = new Response($gres->getJSON());
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }
}