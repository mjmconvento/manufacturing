<?php

namespace Fareast\PurchasingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
// use Catalyst\CoreBundle\Template\Controller\TrackCreate;
// use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
// use Catalyst\PurchasingBundle\Entity\POEntry;
// use Catalyst\PurchasingBundle\Entity\PODelivery;
// use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
// use Catalyst\InventoryBundle\Entity\Product;
// use Catalyst\InventoryBundle\Entity\ProductAttribute;

class PurchaseController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = 'feac_pur_po';
        $this->title = 'Purchase Order';

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
        $this->title = 'Purchase Order';
        $params = $this->getViewParams('', 'feac_pur_po');

        return $this->render('FareastPurchasingBundle:Purchasing:index.html.twig', $params);
    }

    public function addAction()
    {
        $this->title = 'Purchase Order';
        $params = $this->getViewParams('add', 'feac_pur_po');

        return $this->render('FareastPurchasingBundle:Purchasing:add.html.twig', $params);
    }
}