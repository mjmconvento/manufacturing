<?php

namespace Serenitea\PurchasingBundle\Controller;

use Catalyst\PurchasingBundle\Controller\PurchaseOrderController as Controller;


class PurchaseOrderController extends Controller
{
    
    public function __construct()
    {
        $this->route_prefix = 'cat_pur_po';
        $this->title = 'Purchase Order';

        $this->list_title = 'Purchase Orders';
        $this->list_type = 'dynamic';
    }
}
