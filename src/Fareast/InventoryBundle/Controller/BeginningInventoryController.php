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

class BeginningInventoryController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = 'feac_inv_begin';
        $this->title = 'Beginning Inventory';

        // $this->list_title = 'Warehouses';
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

    public function indexAction()
    {
        $this->title = 'Beginning Inventory';
        $params = $this->getViewParams('', 'feac_inv_begin');

        return $this->render('FareastInventoryBundle:Inventory:index.html.twig', $params);
    }
}