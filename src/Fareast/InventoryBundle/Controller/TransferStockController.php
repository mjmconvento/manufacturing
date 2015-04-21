<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransferStockController extends CrudController
{
	public function __construct()
    {
        $this->route_prefix = 'feac_inv_transfer';
        $this->title = 'Transfer Stock';

        $this->list_title = 'Transfer Stocks';
        $this->list_type = 'static';
    }

    protected function newBaseClass()
    {
        
    }

    protected function getObjectLabel($obj)
    {
        
    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'FareastInventoryBundle:TransferStock:index.html.twig';

        $inv = $this->get('catalyst_inventory');

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();
        $params['wh_opts'] = $inv->getWarehouseOptions();
        $params['prodgroup_opts'] = $inv->getProductGroupOptions();  

        return $this->render($twig_file, $params);
    }

    public function getProductAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $prod = $em->getRepository('CatalystInventoryBundle:Product')->findAll();

        $json = array();
        foreach($prod as $p)
        {
            if($p->getID() == $id)
            {
                $json = [
                'prodtype' => $p->getProductType()->getName(),
                'uom' => $p->getUnitOfMeasure(),

                ];
            }
        }

        return new JsonResponse($json);   
    }

    public function getGroupAction($var_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $prod = $em->getRepository('CatalystInventoryBundle:Product')->findAll();

        $data = array();
        foreach($prod as $p)
        {
            if($p->getProductGroup()->getID() == $var_id)
            {
                $data[] = [
                'name' => $p->getName(),
                'id' => $p->getID(),

                ];
            }
        }

        // echo "<pre>";
        // print_r($inc_po);
        // // print_r($ctr);
        // // print_r($date);
        // echo "</pre>";
        // die();

        return new JsonResponse($data);
    }
	
}