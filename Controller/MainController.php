<?php

namespace Catalyst\DashboardBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController;

class MainController extends BaseController
{
    public function indexAction()
    {
        $this->title = 'Dashboard';
        
        $params = $this->getViewParams('', 'cat_dashboard_index');

        return $this->render('CatalystDashboardBundle:Main:index.html.twig', $params);
    }
}
