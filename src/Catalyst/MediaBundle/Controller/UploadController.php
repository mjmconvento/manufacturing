<?php

namespace Catalyst\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UploadController extends Controller
{
    public function uploadAction()
    {
        $um = $this->get('catalyst_media');

        $file = $this->getRequest()->files->get('file');
        $um->addFile($file);
    }

    public function indexAction($name)
    {
        return $this->render('CatalystMediaBundle:Default:index.html.twig', array('name' => $name));
    }
}
