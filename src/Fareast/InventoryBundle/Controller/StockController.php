<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
// use Catalyst\CoreBundle\Template\Controller\TrackCreate;
// use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
// use Catalyst\PurchasingBundle\Entity\POEntry;
// use Catalyst\PurchasingBundle\Entity\PODelivery;
// use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
// use Catalyst\InventoryBundle\Entity\Product;
// use Catalyst\InventoryBundle\Entity\ProductAttribute;

class StockController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = '';
        $this->title = '';
        $this->list_type = 'static';
    }

    protected function newBaseClass()
    {
        // return new Warehouse();
    }

    protected function getObjectLabel($obj)
    {
        // return $obj->getName();
    }

    public function adjustAction()
    {
        $this->title = 'Stock Adjustment';
        $params = $this->getViewParams('', 'feac_inv_adjust');

        return $this->render('FareastInventoryBundle:Stock:adjust.html.twig', $params);
    }

    public function transferAction()
    {
        $this->title = 'Transfer Stock';
        $params = $this->getViewParams('', 'feac_inv_transfer');

        return $this->render('FareastInventoryBundle:Stock:transfer.html.twig', $params);
    }

    public function warehouseAction()
    {
        $this->title = 'Warehouse Stock';
        $params = $this->getViewParams('', 'feac_inv_warehouse');

        return $this->render('FareastInventoryBundle:Stock:warehouse.html.twig', $params);
    }
}