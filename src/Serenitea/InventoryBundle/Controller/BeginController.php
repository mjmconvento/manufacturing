<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class BeginController extends CrudController
{
	public function __construct()
	{
		$this->route_prefix = 'serenitea_begin';
        $this->title = 'Beginning Inventory';

        $this->list_title = 'Beginning Inventory';
        $this->list_type = 'static';
	}

	public function indexAction()
	{
		$this->title = 'Beginning Inventory';
        $params = $this->getViewParams('', 'serenitea_begin_index');

        return $this->render('SereniteaInventoryBundle:Begin:index.html.twig', $params);
	}

	protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }
}