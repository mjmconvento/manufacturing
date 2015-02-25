<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\InventoryBundle\Controller\TransactionController as BaseController;
use Catalyst\InventoryBundle\Entity\Stock;
use Catalyst\InventoryBundle\Entity\Entry;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\ValidationException;

class TransactionController extends BaseController
{
	public function __construct()
	{
        $this->route_prefix = 'ser_inv_begin';
        $this->title = 'Beginning Inventory';

        $this->list_title = 'Beginning Inventory';
        $this->list_type = 'dynamic';
	}

	public function indexAction()
    {
        $this->title = 'Beginning Inventory';
        $params = $this->getViewParams('', 'ser_inv_begin_index');

        $inv = $this->get('catalyst_inventory');
        $params['wh_opts'] = $inv->getWarehouseOptions();
        $params['prod_opts'] = $inv->getProductOptions();

        return $this->render('SereniteaInventoryBundle:Transaction:begin.html.twig', $params);
    }


	protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }
    
    protected function processTransferEntries($data, $prefix)
    {
        $em = $this->getDoctrine()->getManager();


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
            $qty = $data[$prefix . '_expiry'][$index];

            
            $pur->findProductWithExpiry($parentProd,$expiry);
                    
            // product
            $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
            if ($prod == null)
                throw new ValidationException('Could not find product.');
            
            $prod = $pur->findProductWithExpiry($prod,$expiry);
            
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
            $to_ents = $this->processTransferEntries($data, 'to');

            // setup transaction
            $trans = new Transaction();
            $trans->setUserCreate($this->getUser())
                ->setDescription($data['desc']);

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

            return $this->redirect($this->generateUrl('ser_inv_begin_index'));
        }
        catch (InventoryException $e)
        {
            $this->addFlash('error', $e->getMessage());

            return $this->redirect($this->generateUrl('ser_inv_begin_index'));
        }

        $this->addFlash('success', 'Transfer transaction successful.');
        return $this->redirect($this->generateUrl('cat_inv_trans_index'));
    }
}