<?php

namespace Catalyst\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CatalystMediaBundle:Default:index.html.twig', array('name' => $name));
    }
}
