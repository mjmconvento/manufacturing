<?php

namespace Serenitea\TemplateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SereniteaTemplateBundle:Default:index.html.twig', array('name' => $name));
    }
}
