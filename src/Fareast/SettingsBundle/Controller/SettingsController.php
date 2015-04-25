<?php

namespace Fareast\SettingsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
// use Catalyst\CoreBundle\Template\Controller\TrackCreate;
// use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
// use Catalyst\PurchasingBundle\Entity\POEntry;
// use Catalyst\PurchasingBundle\Entity\PODelivery;
// use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
// use Catalyst\InventoryBundle\Entity\Product;
// use Catalyst\InventoryBundle\Entity\ProductAttribute;

class SettingsController extends CrudController
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

    public function customerAction()
    {
        $this->title = 'Customer Management';
        $params = $this->getViewParams('', 'feac_set_cust');

        return $this->render('FareastSettingsBundle:Customer:index.html.twig', $params);
    }

    public function addCustomerAction()
    {
        $this->title = 'Customer Management';
        $params = $this->getViewParams('add', 'feac_set_cust');

        return $this->render('FareastSettingsBundle:Customer:add.html.twig', $params);
    }

    public function supplierAction()
    {
        $this->title = 'Supplier Management';
        $params = $this->getViewParams('', 'feac_set_supp');

        return $this->render('FareastSettingsBundle:Supplier:index.html.twig', $params);
    }

    public function addSupplierAction()
    {
        $this->title = 'Supplier Management';
        $params = $this->getViewParams('add', 'feac_set_supp');

        return $this->render('FareastSettingsBundle:Supplier:add.html.twig', $params);
    }

    public function warehouseAction()
    {
        $this->title = 'Warehouse Management';
        $params = $this->getViewParams('', 'feac_set_wh');

        return $this->render('FareastSettingsBundle:Warehouse:index.html.twig', $params);
    }

    public function addWarehouseAction()
    {
        $this->title = 'Warehouse Management';
        $params = $this->getViewParams('add', 'feac_set_wh');

        return $this->render('FareastSettingsBundle:Warehouse:add.html.twig', $params);
    }

    public function productAction()
    {
        $this->title = 'Product Management';
        $params = $this->getViewParams('', 'feac_set_prod');

        return $this->render('FareastSettingsBundle:Product:index.html.twig', $params);
    }

    public function addProductAction()
    {
        $this->title = 'Product Management';
        $params = $this->getViewParams('add', 'feac_set_prod');

        return $this->render('FareastSettingsBundle:Product:add.html.twig', $params);
    }

    public function rawMatlsAction()
    {
        $this->title = 'Raw Materials Management';
        $params = $this->getViewParams('', 'feac_set_raw');

        return $this->render('FareastSettingsBundle:Raw:index.html.twig', $params);
    }

    public function addRawMatlsAction()
    {
        $this->title = 'Raw Materials Management';
        $params = $this->getViewParams('add', 'feac_set_raw');

        return $this->render('FareastSettingsBundle:Raw:add.html.twig', $params);
    }

    public function categoryAction()
    {
        $this->title = 'Category Management';
        $params = $this->getViewParams('', 'feac_set_cat');

        return $this->render('FareastSettingsBundle:Category:index.html.twig', $params);
    }

    public function assetsAction()
    {
        $this->title = 'Fixed Assets Management';
        $params = $this->getViewParams('', 'feac_set_assets');

        return $this->render('FareastSettingsBundle:Assets:index.html.twig', $params);
    }

    public function addAssetsAction()
    {
        $this->title = 'Fixed Assets Management';
        $params = $this->getViewParams('add', 'feac_set_assets');

        return $this->render('FareastSettingsBundle:Assets:add.html.twig', $params);
    }
}