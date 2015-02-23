<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\InventoryBundle\Controller\ProductGroupController as Controller;
use Catalyst\InventoryBundle\Entity\ProductGroup;
use Catalyst\ValidationException;


class ProductGroupController extends Controller
{
	public function __construct()
    {
        $this->repo = 'CatalystInventoryBundle:ProductGroup';
        parent::__construct();
        $this->route_prefix = 'ser_inv_pg';
        $this->title = 'Category';

        $this->list_title = 'Categories';
        $this->list_type = 'dynamic';
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Code','getCode','code'),
            $grid->newColumn('Name','getName','name'),            
        );        
    }    

    protected function update($o, $data, $is_new = false )
    {            
        if ($o->getName() != $data['name'])
        {
            $em = $this->getDoctrine()->getManager();
            $dupe = $em->getRepository('CatalystInventoryBundle:ProductGroup')->findOneBy(array('name' => $data['name']));
            if ($dupe != null)
                throw new ValidationException('Category name already exists.');
            // if (strlen($data['name']) > 0)
            $o->setName($data['name']);            
        }

        if ($o->getCode() != $data['code'])
        {
            $em = $this->getDoctrine()->getManager();
            $dupe = $em->getRepository('CatalystInventoryBundle:ProductGroup')->findOneBy(array('code' => $data['code']));
            if ($dupe != null)
                throw new ValidationException('Category code already exists.');
        // if (strlen($data['code']) > 0)
            $o->setCode($data['code']);
        }      
    }

}