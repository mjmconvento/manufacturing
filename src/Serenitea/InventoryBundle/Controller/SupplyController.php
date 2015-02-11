<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\InventoryBundle\Controller\ProductController as Controller;
use Catalyst\InventoryBundle\Entity\Product;

class SupplyController extends Controller
{
	public function __construct()
    {
        $this->route_prefix = 'ser_inv_prod';
        $this->title = 'Supply';

        $this->list_title = 'Supplies';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new Product();
    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getSKU();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Item Code','',''),
            $grid->newColumn('Description','',''),
            $grid->newColumn('Specs','',''),
            $grid->newColumn('Product Category','',''),
        );
    }
}
