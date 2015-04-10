<?php

namespace Fareast\SettingsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class RawMaterialsController extends CrudController
{
	public function __construct()
	{
		$this->route_prefix = 'feac_set_raw';
		$this->title = 'Raw Materials Management';
		$this->list_title = 'Raw Materials Management';
		$this->list_type = 'dynamic';
	}

	protected function newBaseClass()
    {
       // return new Customer();
    }

    protected function getObjectLabel($obj)
    {
    	if ($obj == null)
            return '';
        return $obj->getName();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Product Code', 'getCode', 'code'),
            $grid->newColumn('Product Name', 'getName', 'name'),
            $grid->newColumn('Unit of Measurement', 'getUOM', 'uom'),            
            $grid->newColumn('Category', 'getProdGroup', 'prodgroup'),
        );
    }
}