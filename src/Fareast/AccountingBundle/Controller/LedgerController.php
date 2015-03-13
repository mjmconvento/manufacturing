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

class LedgerController extends CrudController
{
    public function __construct() {
        // $this->title = 'General Ledger';
        // $this->list_title = 'General Ledger';
        $this->list_type = 'static';
        $this->route_prefix = 'feac_acctg_gen_ledger';
        // $this->repo = 'CatalystPurchasingBundle:PODelivery';
    }

    protected function newBaseClass()
    {
        // return new PODelivery();
    }
    
    protected function getObjectLabel($obj)
    {
        // if ($obj == null)
        //     return '';
        // return $obj->getCode();
    }

    public function indexAction()
    {
        $this->title = 'General Ledger';
        $params = $this->getViewParams('', 'feac_acctg_gen_ledger');

        return $this->render('FareastAccountingBundle:Ledger:index.html.twig', $params);
    }
}
