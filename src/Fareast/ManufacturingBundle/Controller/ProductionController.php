<?php

namespace Fareast\ManufacturingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
// use Catalyst\CoreBundle\Template\Controller\TrackCreate;
// use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
// use Catalyst\PurchasingBundle\Entity\POEntry;
// use Catalyst\PurchasingBundle\Entity\PODelivery;
// use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
// use Catalyst\InventoryBundle\Entity\Product;
// use Catalyst\InventoryBundle\Entity\ProductAttribute;

class ProductionController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = 'feac_mfg_prod_cal';
        $this->title = 'Production Calendar';
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
        $this->title = 'Production Calendar';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        return $this->render('FareastManufacturingBundle:Production:index.html.twig', $params);
    }

    public function shiftReportAction()
    {
        $this->title = 'Create Shift Report';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        return $this->render('FareastManufacturingBundle:Production:shift-report.html.twig', $params);
    }

    public function dailyConsumptionAction()
    {
        $this->title = 'Daily Consumption';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        return $this->render('FareastManufacturingBundle:Production:daily-consumption.html.twig', $params);
    }
}