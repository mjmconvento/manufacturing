<?php

namespace Serenitea\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SereniteaAdminBundle:Default:index.html.twig', array('name' => $name));
    }
}
