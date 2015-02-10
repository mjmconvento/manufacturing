<?php

namespace Serenitea\AdminBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class BranchController extends CrudController
{
	public function __construct()
	{
		$this->title = 'Branch Management';

		$params = $this->getViewParams('', 'ser_branch_index');

		return $this->render('SereniteaAdminBundle:Main:index.html.twig', $params);
	}

	protected function newBaseClass()
    {        
    }

    protected function getObjectLabel($obj)
    {        
    }
}