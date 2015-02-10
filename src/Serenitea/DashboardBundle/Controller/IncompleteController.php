<?php

namespace Serenitea\DashboardBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController as Controller;

class IncompleteController extends Controller
{
	public function indexAction()
	{
		$this->title = 'Incomplete Requisitions';
		$params = $this->getViewParams('', 'serenitea_dashboard_incomplete');

		return $this->render('SereniteaDashboardBundle:Dashboard:incomplete.html.twig', $params);
	}
}