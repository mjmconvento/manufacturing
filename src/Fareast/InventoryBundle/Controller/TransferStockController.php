<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\Transaction;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransferStockController extends CrudController
{
	public function __construct()
    {
        $this->route_prefix = 'feac_inv_transfer';
        $this->title = 'Transfer Stock';

        $this->list_title = 'Transfer Stocks';
        $this->list_type = 'static';
    }

    protected function newBaseClass()
    {
        
    }

    protected function getObjectLabel($obj)
    {

    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        // $twig_file = 'CatalystInventoryBundle:Transaction:begin.html.twig';
        $twig_file = 'FareastInventoryBundle:TransferStock:index.html.twig';

        $inv = $this->get('catalyst_inventory');

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();
        $params['wh_opts'] = $inv->getWarehouseOptions();
        $params['prodgroup_opts'] = $inv->getProductGroupOptions();  
        // $params['prod_opts'] = $inv->getProductGroupOptions();  

        return $this->render($twig_file, $params);
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

        

        return new JsonResponse($data);
    }

    protected function processTransferEntries($data, $prefix)
    {
        // echo "<pre>";
        // print_r($data);
        // // print_r($ctr);
        // // print_r($date);
        // echo "</pre>";
        // die();
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
            $from_ents = $this->processTransferEntries($data, 'fromform');
            $to_ents = $this->processTransferEntries($data, 'toform');

            // setup transaction
            $trans = new Transaction();
            $trans->setUserCreate($this->getUser())
                ->setDescription("Transferring Items");

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
        return $this->redirect($this->generateUrl('feac_inv_transfer_index'));
    }
	
}