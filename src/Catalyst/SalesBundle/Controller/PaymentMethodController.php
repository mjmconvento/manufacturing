<?php

namespace Catalyst\SalesBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\SalesBundle\Entity\PaymentMethod;
use Catalyst\ValidationException;

class PaymentMethodController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'cat_sales_pm';
        $this->title = 'Payment Method';

        $this->list_title = 'Payment Methods';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new PaymentMethod();
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getName();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Name', 'getName', 'name'),
        );
    }

    protected function update($o, $data, $is_new = false)
    {
        $o->setName($data['name']);
    }
}
