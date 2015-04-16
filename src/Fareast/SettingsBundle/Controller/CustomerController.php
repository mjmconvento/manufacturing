<?php

namespace Fareast\SettingsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class CustomerController extends CrudController
{
	public function __construct()
	{
		$this->route_prefix = 'feac_set_cust';
		$this->title = 'Customer Management';
		$this->list_title = 'Customer Management';
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
            $grid->newColumn('First Name', 'getFirstName', 'first_name'),
            $grid->newColumn('Last Name', 'getLastName', 'last_name'),
            $grid->newColumn('Contact Number', 'getContactNumber', 'number'),
            $grid->newColumn('Email Address', 'getEmail', 'email'),
            $grid->newColumn('Contact Person', 'getContactPerson', 'email'),
        );
    }
}