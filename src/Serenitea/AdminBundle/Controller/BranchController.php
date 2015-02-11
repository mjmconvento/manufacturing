<?php

namespace Serenitea\AdminBundle\Controller;

use Catalyst\InventoryBundle\Controller\WarehouseController;
use Catalyst\InventoryBundle\Entity\Warehouse;
use Catalyst\ValidationException;

class BranchController extends WarehouseController
{
	public function __construct()
	{
		$this->route_prefix = 'ser_branch';
        $this->title = 'Branch';

        $this->list_title = 'Branches';
        $this->list_type = 'dynamic';		
	}

	protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Name', 'getName', 'name'),
            $grid->newColumn('Address', 'getAddress', 'address'),
            $grid->newColumn('ContactNumber', 'getContactNumber', 'contact_num'),            
        );
    }
}