<?php

namespace Serenitea\DashboardBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController as Controller;
use DateTime;

class ExcessiveController extends Controller
{
	public function indexAction()
	{
		$this->title = 'Excess Deliveries';
		$params = $this->getViewParams('', 'serenitea_dashboard_excessive');

		$date_from = new DateTime();
        $date_to = new DateTime();
        $date_from->format("Y-m-d");
        $date_to->format("Y-m-d");
        
        $this->padFormParams($params, $date_from, $date_to);

        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;    

		return $this->render('SereniteaDashboardBundle:Dashboard:excessive.html.twig', $params);
	}

	protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }
}