<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

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

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
    }
	
}