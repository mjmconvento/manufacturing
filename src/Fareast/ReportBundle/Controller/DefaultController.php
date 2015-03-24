<?php

namespace Fareast\ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FareastReportBundle:Default:index.html.twig', array('name' => $name));
    }
}
