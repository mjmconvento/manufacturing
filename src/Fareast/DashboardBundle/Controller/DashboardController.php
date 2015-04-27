<?php

namespace Fareast\DashboardBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
// use Catalyst\CoreBundle\Template\Controller\TrackCreate;
// use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
// use Catalyst\PurchasingBundle\Entity\POEntry;
// use Catalyst\PurchasingBundle\Entity\PODelivery;
// use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
// use Catalyst\InventoryBundle\Entity\Product;
// use Catalyst\InventoryBundle\Entity\ProductAttribute;

class DashboardController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = 'feac_dashboard_index';
        $this->title = 'Dashboard';

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
        $this->title = 'Dashboard';
        $params = $this->getViewParams('', 'feac_dashboard_index');

        return $this->render('FareastDashboardBundle::index.html.twig', $params);
    }
}