<?php

namespace Catalyst\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\InventoryBundle\Entity\Entry;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\ValidationException;
use Catalyst\InventoryBundle\Model\InventoryException;


class StockAdjustmentController extends CrudController
{
	public function __construct()
    {
        $this->route_prefix = 'cat_inv_adjust';
        $this->title = 'Stock Adjustment';

        $this->list_title = 'Stock Adjustment';
        $this->list_type = 'static';
    }

    protected function newBaseClass()
    {
    }

    protected function getObjectLabel($obj)
    {
    }

    // TODO: this should refer to settings but we don't have a way to create
    //       inventory accounts on their own yet
    protected function getAdjustmentAccount()
    {
        $em = $this->getDoctrine()->getManager();

        $config = $this->get('catalyst_configuration');
        $adj_warehouse_id = $config->get('catalyst_warehouse_stock_adjustment');
        $adj_warehouse = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($adj_warehouse_id);

        $acc = $em->getRepository('CatalystInventoryBundle:Account')->find($adj_warehouse->getInventoryAccount()->getID());

        return $acc;
    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'CatalystInventoryBundle:StockAdjustment:index.html.twig';

        $inv = $this->get('catalyst_inventory');

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();
        $params['wh_opts'] = $inv->getWarehouseOptions();
        $params['prodgroup_opts'] = $inv->getProductGroupOptions();  

        return $this->render($twig_file, $params);
    }

    protected function generateAdjustmentEntries($data)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');

        // initialize entries
        $entries = array();

        // warehouse
        $wh_id = $data['from_wh_id'];
        $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($wh_id);
        if ($wh == null)
            throw new ValidationException('Could not find warehouse.');
        $wh_acc = $wh->getInventoryAccount();

        // adjustment account
        $adj_acc = $this->getAdjustmentAccount();

        // process each row
        foreach ($data['prod_id'] as $index => $prod_id)
        {
            // product
            $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
            if ($prod == null)
                throw new ValidationException('Could not find product.');

            // old quantity
            $new_qty = $data['qty'][$index];
            $old_qty = $inv->getStockCount($wh_acc, $prod);

            // same amount, no change
            if ($new_qty == $old_qty)
                continue;

            // entry for warehouse
            $wh_entry = new Entry();
            $wh_entry->setInventoryAccount($wh_acc)
                ->setProduct($prod);

            // entry for adjustment
            $adj_entry = new Entry();
            $adj_entry->setInventoryAccount($adj_acc)
                ->setProduct($prod);

            // check if debit or credit
            if ($new_qty > $old_qty)
            {
                $qty = $new_qty - $old_qty;
                $wh_entry->setDebit($qty);
                $adj_entry->setCredit($qty);
            }
            else
            {
                $qty = $old_qty - $new_qty;
                $wh_entry->setCredit($qty);
                $adj_entry->setDebit($qty);
            }

            $entries[] = $wh_entry;
            $entries[] = $adj_entry;
        }

        return $entries;
    }

    public function addSubmitAction()
    {


        $inv = $this->get('catalyst_inventory');
        $log = $this->get('catalyst_log');

        $em = $this->getDoctrine()->getManager();
        $url = $this->generateUrl('cat_inv_adjust_index');

        $data = $this->getRequest()->request->all();

        $config = $this->get('catalyst_configuration');
        $adj_warehouse_id = $config->get('catalyst_warehouse_stock_adjustment');

        if ($data['from_wh_id'] == $adj_warehouse_id)
        {
            $this->addFlash('error', 'Cannot add to Adjustment Warehouse.');
            return $this->redirect($url);
        }

        try
        {
            // process entries
            $entries = $this->generateAdjustmentEntries($data);

            // setup transaction
            $trans = new Transaction();
            $trans->setUserCreate($this->getUser())
                ->setDescription($data['desc']);

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

            return $this->redirect($url);
        }
        catch (InventoryException $e)
        {
            $this->addFlash('error', $e->getMessage());

            return $this->redirect($url);
        }

        $this->addFlash('success', 'Stock adjustment transaction successful.');
        return $this->redirect($url);
    }
}
