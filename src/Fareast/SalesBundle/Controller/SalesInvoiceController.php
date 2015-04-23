<?php

namespace Fareast\SalesBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class SalesInvoiceController extends CrudController
{
	public function __construct()
	{
		$this->route_prefix = 'feac_sales_invoice';
        $this->title = 'Sales Invoice';

        $this->list_title = 'Sales Invoice';
        $this->list_type = 'dynamic';
	}

	protected function newBaseClass()
    {
        // return new BorrowedItems();
    }

    protected function getObjectLabel($obj)
    {
        // return $obj->getCode();
    }

	public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'FareastSalesBundle:SalesInvoice:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
    }
}