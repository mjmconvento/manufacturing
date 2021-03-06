<?php

namespace Catalyst\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\ProductGroup;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductGroupController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'cat_inv_pg';
        $this->title = 'Group';

        $this->list_title = 'Groups';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new ProductGroup();
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

    public function ajaxGetProductsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $pg = $em->getRepository('CatalystInventoryBundle:ProductGroup')->find($id);
        $prods = $pg->getProducts();

        $data = array();
        foreach($prods as $p)
        {
            $data[] = [
                'id' => $p->getID(),
                'name' => $p->getName(),
            ];
        }

        return new JsonResponse($data);
    }



}


