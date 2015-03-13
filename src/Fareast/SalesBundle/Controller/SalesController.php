<?php

namespace Fareast\SalesBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
// use Catalyst\CoreBundle\Template\Controller\TrackCreate;
// use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
// use Catalyst\PurchasingBundle\Entity\POEntry;
// use Catalyst\PurchasingBundle\Entity\PODelivery;
// use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
// use Catalyst\InventoryBundle\Entity\Product;
// use Catalyst\InventoryBundle\Entity\ProductAttribute;

class SalesController extends CrudController
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

    public function deliveryAction()
    {
        $this->title = 'Delivery';
        $params = $this->getViewParams('', 'feac_sales_del');

        return $this->render('FareastSalesBundle:Delivery:index.html.twig', $params);
    }

    public function addDeliveryAction()
    {
        $this->title = 'Delivery';
        $params = $this->getViewParams('add', 'feac_sales_del');

        return $this->render('FareastSalesBundle:Delivery:add.html.twig', $params);
    }

    public function invoiceAction()
    {
        $this->title = 'Sales Invoice';
        $params = $this->getViewParams('', 'feac_sales_invc');

        return $this->render('FareastSalesBundle:Invoice:index.html.twig', $params);
    }

    public function addInvoiceAction()
    {
        $this->title = 'Sales Invoice';
        $params = $this->getViewParams('add', 'feac_sales_invc');

        return $this->render('FareastSalesBundle:Invoice:add.html.twig', $params);
    }

    public function orderAction()
    {
        $this->title = 'Sales Order';
        $params = $this->getViewParams('', 'feac_sales_order');

        return $this->render('FareastSalesBundle:Order:index.html.twig', $params);
    }

    public function addOrderAction()
    {
        $this->title = 'Sales Order';
        $params = $this->getViewParams('add', 'feac_sales_order');

        return $this->render('FareastSalesBundle:Order:add.html.twig', $params);
    }
}