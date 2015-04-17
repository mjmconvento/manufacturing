<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Fareast\InventoryBundle\Entity\IssuedItem;
use Fareast\InventoryBundle\Entity\IssuedEntry;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use DateTime;

class IssuedController extends CrudController
{
    use TrackCreate;
	public function __construct()
    {        
        $this->route_prefix = 'feac_inv_issued';
        $this->title = 'Issued Item';

        $this->list_title = 'Issued Items';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new IssuedItem();
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getCode();
    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'FareastInventoryBundle:Issued:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        $em = $this->getDoctrine()->getManager();
        $issued = $em->getRepository('FareastInventoryBundle:IssuedItem')->findAll();
        $data = array();
        foreach ($issued as $i) {
            $data[] =[
                'id' => $i->getID(),
                'code' => $i->getCode(),                
                'date_issue' => $i->getDateIssue(),
                'user_create' => $i->getUserCreate()->getName(),
                'count' => $i->getTotalItem(),
            ];
        }

        $params['data'] = $data;

        return $this->render($twig_file, $params);
    }

    protected function padFormParams(&$params, $po = null)
    {
        $em = $this->getDoctrine()->getManager();

        $um = $this->get('catalyst_user');
        $inv = $this->get('catalyst_inventory');
        $params['user_opts'] = $um->getUserOptions(); 
        $params['prod_opts'] = $inv->getProductOptions();
        
        return $params;
    }

    protected function update($o, $data, $is_new = false)
    {
        // echo "<pre>";
        // print_r($data);
        // // print_r($ctr);
        // // print_r($date);
        // echo "</pre>";
        // die();
        
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('catalyst_user');
        $inv = $this->get('catalyst_inventory');

        $o->setIssuedTo($user->findUser($data['user_opts']));
        $o->setDateIssue(new DateTime($data['date_issue']));        
        $this->updateTrackCreate($o, $data, $is_new);

        // clear entries
        $ents = $o->getEntries();
        foreach($ents as $ent)
            $em->remove($ent);
        $o->clearEntries();

        if(isset($data['prod_opts']))
        {
            foreach ($data['prod_opts'] as $index => $prod_id) {
                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);

                $qty = $data['qty'][$index];
                $rmk = $data['remarks'][$index];
                $des = $data['desc'][$index];                
                // instantiate
                    $entry = new IssuedEntry();
                    $entry->setProduct($prod)
                        ->setQuantity($qty)
                        ->setRemarks($rmk)
                        ->setDescription($des);

                    // add entry
                    $o->addEntry($entry);        
            }
        }        
    }

    public function getDeptAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CatalystUserBundle:User')->findAll();

        $data = array();
        foreach($user as $u)
        {
            if($u->getID() == $id)
            {
                $data = [
                'dept' => $u->getWarehouse()->getName()

                ];
            }
        }

        return new JsonResponse($data);   
    }

    public function getProductAction($prod_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CatalystInventoryBundle:Product')->findAll();

        $data = array();
        foreach($user as $u)
        {
            if($u->getID() == $prod_id)
            {
                $data = [
                'uom' => $u->getUnitOfMeasure()
                ];
            }
        }

        return new JsonResponse($data);   
    }

    protected function hookPostSave($obj, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();
        if($is_new){
            $obj->setUserCreate($this->getUser());
            $obj->generateCode();
            $em->persist($obj);
            $em->flush();
        }
    }

}