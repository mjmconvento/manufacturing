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

    protected function processBeginEntries($data, $prefix)
    {        
        $em = $this->getDoctrine()->getManager();

        // figure out setter
        if ($prefix == 'from')
            $setter = 'setCredit';
        else
            $setter = 'setDebit';

        // initialize entries
        $entries = array();

        // check if there's anything to process
        if (!isset($data[$prefix . '_wh_id']))
            return $entries;

        // process it
        foreach ($data[$prefix . '_wh_id'] as $index => $wh_id)
        {
            $prod_id = $data[$prefix . '_prod_id'][$index];
            $qty = $data[$prefix . '_qty'][$index];

            // product
            $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
            if ($prod == null)
                throw new ValidationException('Could not find product.');

            // warehouse
            $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($wh_id);
            if ($wh == null)
                throw new ValidationException('Could not find warehouse.');

            $entry = new Entry();
            $entry->setWarehouse($wh)
                ->setProduct($prod)
                ->$setter($qty);

            $entries[] = $entry;
        }

        return $entries;
    }

    public function addSubmitAction()
    {
        $inv = $this->get('catalyst_inventory');
        $log = $this->get('catalyst_log');

        $em = $this->getDoctrine()->getManager();

        try
        {
            $data = $this->getRequest()->request->all();

            // process entries
            $from_ents = $this->processTransferEntries($data, 'from');
            $to_ents = $this->processTransferEntries($data, 'toform');

            // setup transaction
            $trans = new Transaction();
            $trans->setUserCreate($this->getUser())
                ->setDescription("Adding Items");

            // add entries
            foreach ($from_ents as $ent)
                $trans->addEntry($ent);
            foreach ($to_ents as $ent)
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

            return $this->redirect($this->generateUrl('cat_inv_trans_index'));
        }
        catch (InventoryException $e)
        {
            $this->addFlash('error', $e->getMessage());

            return $this->redirect($this->generateUrl('cat_inv_trans_index'));
        }

        $this->addFlash('success', 'Transfer transaction successful.');
        return $this->redirect($this->generateUrl('feac_inv_begin_index'));
    }
}