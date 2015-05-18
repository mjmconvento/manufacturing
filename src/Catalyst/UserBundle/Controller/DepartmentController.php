<?php

namespace Catalyst\UserBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\UserBundle\Entity\Department;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\CoreBundle\Template\Controller\HasGeneratedID;
use Catalyst\CoreBundle\Template\Controller\HasName;
use Catalyst\InventoryBundle\Template\Controller\HasInventoryAccount;

class DepartmentController extends CrudController
{
    use TrackCreate;
    use HasGeneratedID;
    use HasName;
    use HasInventoryAccount;

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
        
        $this->updateTrackCreate($o, $data, $is_new);
        $this->updateHasGeneratedID($o, $data, $is_new);
        $this->updateHasName($o, $data, $is_new);
        $this->updateHasInventoryAccount($o,$data,$is_new);
      
    }
}
