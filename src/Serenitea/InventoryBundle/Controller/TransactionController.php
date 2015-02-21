<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\InventoryBundle\Controller\TransactionController as BaseController;
use Catalyst\InventoryBundle\Entity\Stock;
use Catalyst\InventoryBundle\Entity\Entry;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\ValidationException;

class TransactionController extends BaseController
{
	public function __construct()
	{
		$this->route_prefix = 'ser_inv_begin';
        $this->title = 'Beginning Inventory';

        $this->list_title = 'Beginning Inventory';
        $this->list_type = 'dynamic';
	}

	// public function indexAction()
	// {
	// 	$this->title = 'Beginning Inventory';
 //        $params = $this->getViewParams('', 'ser_inv_begin_index');

 //        return $this->render('SereniteaInventoryBundle:Transaction:form.html.twig', $params);
	// }

	public function indexAction()
    {
        $this->title = 'Beginning Inventory';
        $params = $this->getViewParams('', 'ser_inv_begin_index');

        $inv = $this->get('catalyst_inventory');
        $params['wh_opts'] = $inv->getWarehouseOptions();
        $params['prod_opts'] = $inv->getProductOptions();

        return $this->render('SereniteaInventoryBundle:Transaction:index.html.twig', $params);
    }


	protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }
}