<?php

namespace Serenitea\OnlineFormsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class RepairController extends CrudController
{
     public function __construct()
    {
        $this->route_prefix = 'serenitea_repair';
        $this->title = 'Job Order Forms';

        $this->list_title = 'Job Order Forms';
        $this->list_type = 'static';
    }
    
 
    public function indexAction()
    {
        $this->title = 'Job Order Forms';
        $params = $this->getViewParams('', 'serenitea_repair_index');
        
        $this->csv = 'serenitea_repair_csv';
        
        $params['csv'] = $this->csv;
        
        return $this->render('SereniteaOnlineFormsBundle:Repair:index.html.twig', $params);
    }

    public function headers()
    {
        // csv headers
        $headers = [
            'Job Order #',
            'Date Created',            
            'Created By',            
        ];
        return $headers;
    }
    
    public function csvAction(){

        // filename generate
        $date = new DateTime();
        $filename = $date->format('Ymdis') . '.csv';

        // redirect file to stdout, store in output buffer and place in $csv
        $file = fopen('php://output', 'w');
        ob_start();

        $csv_headers = $this->headers();

        fputcsv($file, $csv_headers);

        fclose($file);


        // csv header
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);

        return $response;
    }      
    
    public function addFormAction()
    {
        $this->checkAccess($this->route_prefix . '.add');

        $this->hookPreAction();
        $obj = $this->newBaseClass();

        $params = $this->getViewParams('Add');
        $params['object'] = $obj;


        // Getting super user
        $prodgroup = $this->get('catalyst_configuration');
        $repository = $this->getDoctrine()
            ->getRepository('CatalystUserBundle:Group');
        $super_user = $repository->findOneById($prodgroup->get('catalyst_super_user_role_default'));    
        $params['super_user'] = $super_user;


        // check if we have access to form
        $params['readonly'] = !$this->getUser()->hasAccess($this->route_prefix . '.add');

        $this->padFormParams($params, $obj);

        return $this->render('SereniteaOnlineFormsBundle:Repair:add.html.twig', $params);
    }
    
    protected function getObjectLabel($obj) {
        
    }

    protected function newBaseClass() {
        
    }
    
     public function viewAction(){
        $this->title = 'Job Order Forms';
        $params = $this->getViewParams('', 'serenitea_repair_view');
        return $this->render('SereniteaOnlineFormsBundle:Repair:view.html.twig', $params);
    }

}
