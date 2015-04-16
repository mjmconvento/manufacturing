<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Fareast\InventoryBundle\Entity\BorrowedItem;
use Fareast\InventoryBundle\Entity\BorrowedEntry;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class BorrowedItemsController extends CrudController
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

        $twig_file = 'FareastInventoryBundle:Borrowed:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        $date_from = new DateTime();
        $date_to = new DateTime();
        $date_from->format("Y-m-d");
        $date_to->format("Y-m-d");

        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;

        $this->padFormParams($params, $date_from, $date_to);



        return $this->render($twig_file, $params);
    }

    public function addFormAction()
    {
        $this->checkAccess($this->route_prefix . '.add');        

        $this->hookPreAction();
        $obj = $this->newBaseClass();

        $params = $this->getViewParams('Add');
        $params['object'] = $obj;

        // check if we have access to form
        $params['readonly'] = !$this->getUser()->hasAccess($this->route_prefix . '.add');
        $this->padFormParams($params, $obj);

        // $em = $this->getDoctrine()->getManager();
        $um = $this->get('catalyst_user');
        $inv = $this->get('catalyst_inventory');
        $params['user_opts'] = $um->getUserOptions(); 
        $params['prod_opts'] = $inv->getProductOptions();

        return $this->render('FareastInventoryBundle:Borrowed:form.html.twig', $params);
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

        // clear entries
        $ents = $o->getEntries();
        foreach($ents as $ent)
            $em->remove($ent);
        $o->clearEntries();

        
                // $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                // if ($prod == null)
                //     throw new ValidationException('Could not find product.');
            $prod = $data['prod_opts'];
                $prod = $inv->findProduct($prod);
                $qty = $data['qty'];
                $rmk = $data['remarks'];
                $des = $data['desc'];                
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