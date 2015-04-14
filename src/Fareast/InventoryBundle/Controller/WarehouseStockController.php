<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class WarehouseStockController extends CrudController
{
	public function __construct()
    {        
        $this->route_prefix = 'feac_inv_warehouse';
        $this->title = 'Warehouse Stock';

        $this->list_title = 'Warehouse Stocks';
        $this->list_type = 'dynamic';
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

        $twig_file = 'FareastInventoryBundle:WarehouseStock:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
    }
}