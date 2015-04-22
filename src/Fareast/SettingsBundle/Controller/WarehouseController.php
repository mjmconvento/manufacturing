<?php

namespace Fareast\SettingsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class WarehouseController extends CrudController
{
	public function __construct()
	{
		$this->route_prefix = 'feac_set_wh';
		$this->title = 'Warehouse Management';
		$this->list_title = 'Warehouse Management';
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
            $grid->newColumn('Branch Code', 'getCode', 'code'),
            $grid->newColumn('Branch Name', 'getName', 'name'),
            $grid->newColumn('Address', 'getAddress', 'email'),
            $grid->newColumn('Contact Number', 'getContactNumber', 'number'),                        
        );
    }
}