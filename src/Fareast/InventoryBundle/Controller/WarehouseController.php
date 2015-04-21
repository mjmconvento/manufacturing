<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\InventoryBundle\Controller\WarehouseController as BaseController;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\CoreBundle\Template\Controller\TrackUpdate;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\ValidationException;

class WarehouseController extends BaseController
{
    use TrackUpdate;
    use TrackCreate;

    public function __construct()
    {	
        $this->repo = 'CatalystInventoryBundle:Warehouse';
        parent::__construct();      
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
        if (empty($data['name']))
            throw new ValidationException('Cannot leave name blank');
            
        $o->setName($data['name'])
            ->setType($data['type_id']);

        if ($is_new == true)
        {
            $o->setUserCreate($this->getUser());
        }
        
        $this->updateHasInventoryAccount($o,$data,$is_new);
        
        if (isset($data['flag_threshold']) && $data['flag_threshold'])
            $o->setFlagThreshold();
        else
            $o->setFlagThreshold(false);

        if (isset($data['flag_shopfront']) && $data['flag_shopfront'])
            $o->setFlagShopfront();
        else
            $o->setFlagShopfront(false);

   
    }

    protected function getStockColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Name', 'getName', 'name', 'p'),
            $grid->newColumn('Quantity', 'getQuantity', 'quantity'),
        );
    }

    protected function setupStockGrid($id)
    {
        $grid = $this->get('catalyst_grid');
        $data = $this->getRequest()->query->all();
        $em = $this->getDoctrine()->getManager();

        // setup grid
        $gl = $grid->newLoader();
        $gl->processParams($data)
            ->setRepository('CatalystInventoryBundle:Stock')
            ->addJoin($grid->newJoin('p', 'product', 'getProduct'))
            ->enableCountFilter();

        // columns
        $stock_cols = $this->getStockColumns();
        foreach ($stock_cols as $col)
            $gl->addColumn($col);

        return $gl;
    }

    public function stockGridAction($id)
    {
        $gl = $this->setupStockGrid($id);
        $gres = $gl->load();

        $resp = new Response($gres->getJSON());
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }
    
}