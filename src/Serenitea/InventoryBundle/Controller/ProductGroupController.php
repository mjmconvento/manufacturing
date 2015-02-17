<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\InventoryBundle\Controller\ProductGroupController as Controller;
use Catalyst\ValidationException;


class ProductGroupController extends Controller
{
	public function __construct()
    {
        $this->repo = 'CatalystInventoryBundle:ProductGroup';
        parent::__construct();
        $this->route_prefix = 'ser_inv_pg';
        $this->title = 'Category';

        $this->list_title = 'Categories';
        $this->list_type = 'dynamic';
    }    

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Code','getCode','code'),
            $grid->newColumn('Name','getName','name'),            
        );        
    }

    protected function update($o, $data, $is_new = false )
    {            
        // validate name
        if (strlen($data['name']) > 0)
            $o->setName($data['name']);
        else
            throw new ValidationException('Cannot leave name blank');

        //validate code
        if (strlen($data['code']) > 0)
            $o->setCode($data['code']);
        else
            throw new ValidationException('Cannot leave code blank');

    }    
}