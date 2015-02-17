<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController as Controller;
use Serenitea\InventoryBundle\Entity\ProductCategory;
use Catalyst\ValidationException;


class CategoryController extends Controller
{
	public function __construct()
    {
        $this->route_prefix = 'ser_inv_pg';
        $this->title = 'Category';

        $this->list_title = 'Categories';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new ProductCategory();
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getName();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Item Code','getCode','code'),
            $grid->newColumn('Name','getName','name'),
        );
    }

    protected function padFormParams(&$params, $o = null)
    {
        $em = $this->getDoctrine()->getManager();
        $prodcat = $em->getRepository('SereniteaInventoryBundle:ProductCategory')->findAll();
        $pc_opts = array();
        foreach($prodcat as $prod)
            $pc_opts[$prod->getID()] = $prod->getName();

        $params['pc_opts'] = $pc_opts;

        return $params;
    }

    public function update($o, $data, $is_new = false )
    {
        //TODO: check if the name already exist
        // validate name
        if (strlen($data['name']) > 0)
            $o->setName($data['name']);
        else
            throw new ValidationException('Cannot leave name blank');

        //validate code
        if (strlen($data['code']) > 0)
            $o->setCode($data['code']);
        else
            throw new ValidationException('Cannot leave code blank');

    }
}