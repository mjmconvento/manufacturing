<?php

namespace Fareast\ReceivingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
// use Catalyst\CoreBundle\Template\Controller\TrackCreate;
// use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
// use Catalyst\PurchasingBundle\Entity\POEntry;
// use Catalyst\PurchasingBundle\Entity\PODelivery;
// use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
// use Catalyst\InventoryBundle\Entity\Product;
// use Catalyst\InventoryBundle\Entity\ProductAttribute;

class ReceivingController extends CrudController
{
   public function __construct()
    {
        $this->route_prefix = 'feac_rcvng_orders';
        $this->title = 'Receiving Orders';

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
        $this->title = 'Receiving Orders';
        $params = $this->getViewParams('', 'feac_rcvng_orders');

        return $this->render('FareastReceivingBundle:Receiving:index.html.twig', $params);
    }

    public function viewAction()
    {
        $this->title = 'Received Purchase Order';
        $params = $this->getViewParams('view', 'feac_rcvng_orders');

        return $this->render('FareastReceivingBundle:Receiving:view.html.twig', $params);
    }
}