<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class BeginController extends CrudController
{
	public function __construct()
	{
		$this->route_prefix = 'ser_begin';
        $this->title = 'Beginning Inventory';

        $this->list_title = 'Beginning Inventory';
        $this->list_type = 'dynamic';
	}

	public function indexAction()
	{
		$this->title = 'Beginning Inventory';
        $params = $this->getViewParams('', 'ser_begin_index');

        return $this->render('SereniteaInventoryBundle:Begin:form.html.twig', $params);
	}

	protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }
}