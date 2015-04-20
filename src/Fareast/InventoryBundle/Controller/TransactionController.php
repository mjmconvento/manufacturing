<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\InventoryBundle\Controller\TransactionController as BaseController;
use Catalyst\InventoryBundle\Entity\Stock;
use Catalyst\InventoryBundle\Entity\Entry;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\ValidationException;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends BaseController
{    
    public function __construct()
    {
        $this->route_prefix = 'feac_inv_begin';
        $this->title = 'Beginning Inventory';

        $this->list_title = 'Beginning Inventory';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        
    }

    protected function getObjectLabel($obj)
    {
        
    }

    public function indexAction()
    {
        $this->title = 'Beginning Inventory';
        $params = $this->getViewParams('', 'feac_inv_begin_index');

        $inv = $this->get('catalyst_inventory');
        $params['wh_opts'] = $inv->getWarehouseOptions();
        $params['prodgroup_opts'] = $inv->getProductGroupOptions();        

        return $this->render('FareastInventoryBundle:Transaction:index.html.twig', $params);
    }

    public function getGroupAction($var_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $prod = $em->getRepository('CatalystInventoryBundle:Product')->findAll();

        $data = array();
        foreach($prod as $p)
        {
            if($p->getProductGroup()->getID() == $var_id)
            {
                $data[] = [
                'name' => $p->getName(),
                'id' => $p->getID(),

                ];
            }
        }

        // echo "<pre>";
        // print_r($inc_po);
        // // print_r($ctr);
        // // print_r($date);
        // echo "</pre>";
        // die();

        return new JsonResponse($data);
    }

    public function getProductAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $prod = $em->getRepository('CatalystInventoryBundle:Product')->findAll();

        $json = array();
        foreach($prod as $p)
        {
            if($p->getID() == $id)
            {
                $json = [
                'prodtype' => $p->getProductType()->getName(),
                'uom' => $p->getUnitOfMeasure(),

                ];
            }
        }

        return new JsonResponse($json);   
    }

    public function addSubmitAction()
    {

        $inv = $this->get('catalyst_inventory');
        $log = $this->get('catalyst_log');

        $em = $this->getDoctrine()->getManager();

        try
        {

            $data = $this->getRequest()->request->all();

            $entries = $this->processBeginEntries($data);
            // setup transaction
            $trans = new Transaction();
            $trans->setUserCreate($this->getUser())
                ->setDescription("Adding Items");

            // add entries
            foreach ($entries as $ent)
                $trans->addEntry($ent);

            $inv->persistTransaction($trans);
            $em->flush();

            // log
            $odata = $trans->toData();
            $log->log('cat_inv_trans_add', 'added Inventory Transaction ' . $trans->getID() . '.', $odata);
        }
        catch (ValidationException $e)
        {
            $this->addFlash('error', $e->getMessage());
        }
        catch (InventoryException $e)
        {
            $this->addFlash('error', $e->getMessage());
        }

        $this->addFlash('success', 'Transfer transaction successful.');
        return $this->redirect($this->generateUrl('feac_inv_begin_index'));
    }

    protected function processBeginEntries($data)
    {        
        $em = $this->getDoctrine()->getManager();        
        $inv = $this->get('catalyst_inventory');
        $conf = $this->get('catalyst_configuration');        

        $supp = $data['wh_opts'];

        $supplier = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($supp);
            if ($supplier == null)
                throw new ValidationException('Could not find warehouse.'); 

        //     echo "<pre>";
        // print_r($data);
        // print_r($supplier);
        // // print_r($date);
        // echo "</pre>";
        // die();
        
        // initialize entries
        $entries = array();

        // check if there's anything to process
        if (!isset($data['prod_id']))
            return $entries;

        // process it
        foreach ($data['prod_id'] as $index => $prod_id)
        {
            $qty = $data['qty'][$index];

            
            // product
            $product = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
            if ($product == null)
                throw new ValidationException('Could not find product.'); 

                                 
                        
            $entries = array_merge($entries,$inv->itemsIn($product,$qty,$supplier));
        }
        return $entries;
    }
}