<?php

namespace Fareast\SalesBundle\Controller;

use Catalyst\SalesBundle\Controller\SalesOrderController as BaseController;

class SalesOrderController extends BaseController
{
   public function __construct()
    {
        $this->repo = 'CatalystSalesBundle:SalesOrder';
        parent::__construct();
    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'FareastSalesBundle:SalesOrder:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
    }
}