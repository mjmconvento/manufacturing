<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;


class StockAdjustmentController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = 'feac_inv_adjust';
        $this->title = 'Stock Adjustment';

        $this->list_title = 'Stock Adjustments';
        $this->list_type = 'static';
    }

    protected function newBaseClass()
    {
        
    }

    protected function getObjectLabel($obj)
    {
        
    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'FareastInventoryBundle:StockAdjustment:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
    }

    public function adjustAction()
    {
        $this->title = 'Stock Adjustment';
        $params = $this->getViewParams('', 'feac_inv_adjust');

        return $this->render('FareastInventoryBundle:Stock:adjust.html.twig', $params);
    }

    public function transferAction()
    {
        $this->title = 'Transfer Stock';
        $params = $this->getViewParams('', 'feac_inv_transfer');

        return $this->render('FareastInventoryBundle:Stock:transfer.html.twig', $params);
    }

    public function warehouseAction()
    {
        $this->title = 'Warehouse Stock';
        $params = $this->getViewParams('', 'feac_inv_warehouse');

        return $this->render('FareastInventoryBundle:Stock:warehouse.html.twig', $params);
    }
}