<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class TransactionController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = 'feac_inv_begin';
        $this->title = 'Beginning Inventory';

        $this->list_title = 'Beginning Inventory';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        
    }

    protected function getObjectLabel($obj)
    {
        
    }

    public function indexAction()
    {
        $this->title = 'Beginning Inventory';
        $params = $this->getViewParams('', 'feac_inv_begin_index');

        // $inv = $this->get('catalyst_inventory');
        // $pur = $this->get('catalyst_purchasing');
        // $params['wh_opts'] = $inv->getWarehouseOptions();
        // $params['prod_opts'] = $inv->getProductOptions();
        // $params['supplier_opts'] = array_merge(array('0'=> 'No Supplier' ),$pur->getSupplierOptions());

        return $this->render('FareastInventoryBundle:Transaction:index.html.twig', $params);
    }
}