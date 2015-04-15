<?php

namespace Catalyst\PurchasingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\PurchasingBundle\Entity\Supplier;
use Catalyst\ValidationException;

use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\InventoryBundle\Template\Controller\HasInventoryAccount;
use Catalyst\ContactBundle\Template\Controller\HasContactInfo;

class SupplierController extends CrudController
{
    use TrackCreate;
    use HasContactInfo;
    use HasInventoryAccount;

    public function __construct()
    {
        $this->route_prefix = 'cat_pur_supp';
        $this->title = 'Supplier';

        $this->list_title = 'Suppliers';
        $this->list_type = 'static';
        $this->view_path = 'CatalystPurchasingBundle:Supplier';
    }

    protected function newBaseClass()
    {
        return new Supplier();
    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getDisplayName();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Name', 'getName', 'last_name'),
            $grid->newColumn('Email', 'getEmail', 'email'),
        );
    }

    protected function padFormParams(&$params, $o = null)
    {
        $inv = $this->get('catalyst_inventory');

        $params['wh_opts'] = $inv->getWarehouseOptions();
        $this->padFormContactInfo($params);
        
        return $params;
    }

    protected function update($o, $data, $is_new = false)
    {
        $this->updateTrackCreate($o, $data, $is_new);
        $this->updateContact($o, $data, $is_new);
        $this->updateHasInventoryAccount($o, $data, $is_new);
    }
    
    protected function buildData($o)
    {
        $data = $o->toData();
        return $data;
    }
}
