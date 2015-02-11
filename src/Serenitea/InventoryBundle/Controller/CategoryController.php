<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\InventoryBundle\Controller\ProductGroupController as Controller;
use Catalyst\InventoryBundle\Entity\ProductGroup;

class CategoryController extends Controller
{
	public function __construct()
    {
        $this->route_prefix = 'ser_inv_pg';
        $this->title = 'Category';

        $this->list_title = 'Categories';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new ProductGroup();
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getName();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Item Code','',''),
            $grid->newColumn('Name','',''),
        );
    }
}