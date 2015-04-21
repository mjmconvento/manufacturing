<?php

namespace Catalyst\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class UploadController extends Controller
{
    public function uploadAction()
    {
        $em = $this->getDoctrine()->getManager();
        $um = $this->get('catalyst_media');

        $file = $this->getRequest()->files->get('file');
        $upload = $um->addFile($file);

        if ($upload == null)
        {
            $res = array();
            return new JsonResponse($res);
        }

        $res = array(
            'id' => $upload->getID(),
            'filename' => $upload->getFilename(),
            'url' => $upload->getURL()
        );
        return new JsonResponse($res);
    }

    public function indexAction($name)
    {
        return $this->render('CatalystMediaBundle:Default:index.html.twig', array('name' => $name));
    }
}
