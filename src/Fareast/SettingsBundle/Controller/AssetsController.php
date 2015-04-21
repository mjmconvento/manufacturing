<?php

namespace Fareast\SettingsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class AssetsController extends CrudController
{
	public function __construct()
	{
		$this->route_prefix = 'feac_set_assets';
		$this->title = 'Fixed Assets Management';
		$this->list_title = 'Fixed Assets Management';
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
            $grid->newColumn('Item Code', 'getCode', 'code'),
            $grid->newColumn('Item Name', 'getName', 'name'),
            $grid->newColumn('Unit of Measurement', 'getUOM', 'uom'),            
            $grid->newColumn('Category', 'getProdGroup', 'prodgroup'),
        );
    }
}