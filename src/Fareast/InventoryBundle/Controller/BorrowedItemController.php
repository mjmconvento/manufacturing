<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\BorrowedItem;
use Catalyst\InventoryBundle\Entity\BIEntry;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class BorrowedItemController extends CrudController
{
    use TrackCreate;
    public function __construct()
    {        
        $this->route_prefix = 'feac_inv_borrowed';
        $this->title = 'Borrowed Item';

        $this->list_title = 'Borrowed Items';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new BorrowedItem();
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

        $twig_file = 'FareastInventoryBundle:BorrowedItem:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        $date_from = new DateTime();
        $date_to = new DateTime();
        $date_from->format("Y-m-d");
        $date_to->format("Y-m-d");

        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;

        $em = $this->getDoctrine()->getManager();
        $borrowed = $em->getRepository('CatalystInventoryBundle:BorrowedItem')->findAll();
        $data = array();
        foreach ($borrowed as $b) {
            $data[] =[
                'id' => $b->getID(),
                'code' => $b->getCode(),
                'borrower' => $b->getIssuedTo()->getName(),
                'date_issue' => $b->getDateIssue(),
                'user_create' => $b->getUserCreate()->getName(),
                'status' => $b->getStatus(),
                'count' => $b->getTotalItem(),
            ];
        }

        $params['data'] = $data;

        $this->padFormParams($params, $date_from, $date_to);



        return $this->render($twig_file, $params);
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
                'dept' => $u->getDepartment(),

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

    protected function padFormParams(&$params, $po = null)
    {
        $em = $this->getDoctrine()->getManager();

        $um = $this->get('catalyst_user');
        $inv = $this->get('catalyst_inventory');
        $params['user_opts'] = $um->getUserOptions(); 
        $params['prod_opts'] = $inv->getProductOptions();

        $params['status_opts'] = array('Incomplete'=>  BorrowedItem::STATUS_INCOMPLETE, 'Complete'=>  BorrowedItem::STATUS_COMPLETE);        
        
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
        $o->setDateReturned(new DateTime($data['date_return']));
        $this->updateTrackCreate($o, $data, $is_new);
        $o->setStatus($data['status']);

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
                    $entry = new BorrowedEntry();
                    $entry->setProduct($prod)
                        ->setQuantity($qty)
                        ->setRemarks($rmk)
                        ->setDescription($des);

                    // add entry
                    $o->addEntry($entry);        
            }
        }        
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