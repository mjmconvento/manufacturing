<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController as Controller;
use Serenitea\InventoryBundle\Entity\Supply;
use Catalyst\ValidationException;

class SupplyController extends Controller
{
	public function __construct()
    {
        $this->route_prefix = 'ser_inv_prod';
        $this->title = 'Supply';

        $this->list_title = 'Supplies';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new Supply();
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getName();
    }

    protected function getGridJoins()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newJoin('pc','prodcat','getProductCategory'),
            );
    }

    protected function padFormParams(&$params, $o = null)
    {
        $em = $this->getDoctrine()->getManager();

        $prodcat = $em->getRepository('SereniteaInventoryBundle:ProductCategory')->findAll();
        $pc_opts = array();
        foreach($prodcat as $prod)
            $pc_opts[$prod->getID()] = $prod->getName();

        $supps = $em->getRepository('CatalystPurchasingBundle:Supplier')->findAll();
        $supp_opts = array();
        foreach ($supps as $supp)
            $supp_opts[$supp->getID()] = $supp->getDisplayName();

        $params['supp_opts'] = $supp_opts;
        $params['pc_opts'] = $pc_opts;
        

        return $params;
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Item Code','getSKU','sku'),
            $grid->newColumn('Description','getName','name'),
            $grid->newColumn('Specs','getUnitOfMeasure','uom'),
            $grid->newColumn('Product Category','getProductCategory','name', 'pg'),
        );
    }

    public function update($o, $data, $is_new = false)
    {
        //validate name
        if (strlen($data['name']) > 0)
            $o->setName($data['name']);
        else
            throw new ValidationException('Cannot leave name blank');

        $o->setSupplier($data['supplier_id']);
        $o->setProductCategory($data['prod_cat_id']);
        if(strlen($data['supp_code']) > 0)
            $o->setSupplierCode($data['supp_code']);
        else
            throw new ValidationException("Cannot leave supplier's code blank");
            
        $o->setUnitOfMeasure($data['uom']);

        // prices
        $o->setPriceSale($data['price_sale']);
        $o->setPricePurchase($data['price_purchase']);
    }
}
