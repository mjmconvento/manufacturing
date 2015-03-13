<?php

namespace Fareast\AccountingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
// use Catalyst\CoreBundle\Template\Controller\TrackCreate;
// use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
// use Catalyst\PurchasingBundle\Entity\POEntry;
// use Catalyst\PurchasingBundle\Entity\PODelivery;
// use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
// use Catalyst\InventoryBundle\Entity\Product;
// use Catalyst\InventoryBundle\Entity\ProductAttribute;

class ChartsController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = 'feac_actg_chart_accts';
        $this->title = 'Chart of Accounts';

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
        $this->title = 'Chart of Accounts';
        $params = $this->getViewParams('', 'feac_actg_chart_accts');

        return $this->render('FareastAccountingBundle:Charts:index.html.twig', $params);
    }
}