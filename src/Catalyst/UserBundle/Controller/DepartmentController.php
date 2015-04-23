<?php

namespace Catalyst\UserBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\UserBundle\Entity\Department;

class DepartmentController extends CrudController
{
	public function __construct()
    {
        $this->route_prefix = 'cat_user_dept';
        $this->title = 'Department';

        $this->list_title = 'Departments';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new Department('');
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
        // validate name
        if (strlen($data['name']) > 0)
            $o->setName($data['name']);
        else
            throw new ValidationException('Cannot leave name blank');       
    }
}