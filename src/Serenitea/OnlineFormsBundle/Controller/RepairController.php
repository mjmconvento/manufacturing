<?php

namespace Serenitea\OnlineFormsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Serenitea\OnlineFormsBundle\Entity\Repair;
use DateTime;

class RepairController extends CrudController
{
     public function __construct()
    {
        $this->route_prefix = 'ser_repair';
        $this->title = 'Job Order Form';

        $this->list_title = 'Job Order Forms';
        $this->list_type = 'dynamic';
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Job Order No.','getCode','code'),
            $grid->newColumn('Date Created','getDateCreateFormatted','date_create'),
            $grid->newColumn('Created By','getName','name','u'),
            $grid->newColumn('Status','getStatus','status'),
        );
    }
    
    protected function padFormParams(&$params, $object = null)
    {
        $object->setWarehouse($this->getUser()->getWarehouse());
    
        $params['status_opts'] = array('Draft'=>'Draft',
                                    'Sent'=>'Sent');
    }
    
    
    protected function getGridJoins()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newJoin('u', 'user_create', 'getUserCreate'),
        );
    }
 

    protected function getObjectLabel($obj) 
    {
        return $obj->getCode();
    }

    protected function newBaseClass() 
    {
        return new Repair();
    }
    
    protected function hookPostSave($obj,$is_new = false) 
    {
        $em = $this->getDoctrine()->getManager();
        if($is_new){
            $obj->generateCode();
            $em->persist($obj);
            $em->flush();
        }
       
    }

}
