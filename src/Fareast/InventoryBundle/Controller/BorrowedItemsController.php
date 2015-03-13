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

class BorrowedItemsController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = 'feac_inv_borrowed';
        $this->title = 'Pull Out';

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
        $this->title = 'Pull Out';
        $params = $this->getViewParams('', 'feac_inv_borrowed');

        return $this->render('FareastInventoryBundle:Borrowed:index.html.twig', $params);
    }

    public function addAction()
    {
        $this->title = 'Pull Out';
        $params = $this->getViewParams('add', 'feac_inv_borrowed');

        return $this->render('FareastInventoryBundle:Borrowed:add.html.twig', $params);
    }

    public function returnedAction()
    {
        $this->title = 'Returning';
        $params = $this->getViewParams('add', 'feac_inv_borrowed');

        return $this->render('FareastInventoryBundle:Borrowed:returned.html.twig', $params);
    }
}