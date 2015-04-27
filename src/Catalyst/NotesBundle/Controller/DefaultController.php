<?php

namespace Catalyst\NotesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CatalystNotesBundle:Default:index.html.twig');
    }
}
