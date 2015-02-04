<?php

namespace Catalyst\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CatalystGalleryBundle:Default:index.html.twig', array('name' => $name));
    }
}
