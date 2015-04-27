<?php

namespace Catalyst\AccountingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CatalystAccountingBundle:Default:index.html.twig', array('name' => $name));
    }
}
