<?php

namespace Fareast\SettingsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Fareast\SettingsBundle\Entity\Supplier;

class SupplierController extends CrudController
{
	public function __construct()
	{
		$this->route_prefix = 'feac_set_supp';
		$this->title = 'Supplier Management';
		$this->list_title = 'Supplier Management';
		$this->list_type = 'dynamic';
	}

	protected function newBaseClass()
    {
       return new Supplier();
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
            $grid->newColumn('Company Name', 'getName', 'last_name'),
            $grid->newColumn('Address', 'getAddress', 'code'),
            $grid->newColumn('Contact Number', 'getContactNumber', 'number'),
            $grid->newColumn('Tin Number', 'getTinNumber', 'tin'),
        );
    }
}