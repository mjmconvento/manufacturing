<?php

namespace Catalyst\PurchasingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\PurchasingBundle\Entity\Supplier;
use Catalyst\ValidationException;

use Catalyst\CoreBundle\Model\Controller\TrackCreate;
use Catalyst\ContactBundle\Model\Controller\Contact;

class SupplierController extends CrudController
{
    use TrackCreate;
    use Contact;

    public function __construct()
    {
        $this->route_prefix = 'cat_pur_supp';
        $this->title = 'Supplier';

        $this->list_title = 'Suppliers';
        $this->list_type = 'static';
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
            $grid->newColumn('Name', 'getDisplayName', 'last_name'),
            $grid->newColumn('Email', 'getEmail', 'email'),
        );
    }

    protected function padFormParams(&$params, $o = null)
    {
        $inv = $this->get('catalyst_inventory');

        $params['wh_opts'] = $inv->getWarehouseOptions(array('type_id' => 'virtual'));

        return $params;
    }

    protected function update($o, $data, $is_new = false)
    {
        $this->updateTrackCreate($o, $data, $is_new);
        $this->updateContact($o, $data, $is_new);
    }

    protected function buildData($o)
    {
        $data = array(
            'id' => $o->getID(),
            'name' => $o->getName(),
            'address' => $o->getAddress(),
            'contact_number' => $o->getContactNumber(),
            'email' => $o->getEmail(),
            'contact_person' => $o->getContactPerson(),
            'notes' => $o->getNotes(),
        );

        return $data;
    }
}
