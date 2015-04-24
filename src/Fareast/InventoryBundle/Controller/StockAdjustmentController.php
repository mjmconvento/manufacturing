<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\InventoryBundle\Entity\Warehouse;
use Catalyst\InventoryBundle\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockAdjustmentController extends BaseController
{
    public function indexAction()
    {
        $this->title = 'Stock Adjustment';
        $this->route_prefix = 'feac_inv_adjust';

        $params = $this->getViewParams('','feac_inv_adjust_index');

        $em = $this->getDoctrine()->getManager();

        $inv = $this->get('catalyst_inventory');
        $params['dept'] = $inv->getWarehouseOptions();
        $params['prodgroup_opts'] = $inv->getProductGroupOptions();

        $stock = $em->getRepository('CatalystInventoryBundle:Stock')->findAll();

        $data = array();
        foreach($stock as $s)
        {
            if($s->getQuantity() > 0)
            $data[] = [
                'code' => $s->getProduct()->getSKU(),
                'warehouse' => $s->getInventoryAccount()->getName(),
                'wh_id' => $s->getInventoryAccount()->getID(),
                'name' => $s->getProduct()->getName(),
                'prod_id' => $s->getProduct()->getID(),
                'quantity' =>$s->getQuantity(),
                'uom' => $s->getProduct()->getUnitOfMeasure(),
            ];
        }

        $params['data'] = $data;

        return $this->render('FareastInventoryBundle:StockAdjustment:index.html.twig', $params);
    }

    public function filterAction($dept = null, $category = null)
    {
        
    }

    protected function processTransferEntries($data, $prefix)
    {
        echo "<pre>";
        print_r($data);
        // print_r($ctr);
        // print_r($date);
        echo "</pre>";
        die(); 
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
        return $this->redirect($this->generateUrl('feac_inv_adjust_index'));
    }
}