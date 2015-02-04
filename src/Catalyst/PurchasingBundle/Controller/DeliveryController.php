<?php

namespace Catalyst\PurchasingBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController;
use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
use Catalyst\PurchasingBundle\Entity\POEntry;
use Catalyst\PurchasingBundle\Entity\PODelivery;
use Catalyst\PurchasingBundle\Entity\PODeliveryEntry;
use Catalyst\InventoryBundle\Entity\Product;

use Catalyst\InventoryBundle\Entity\InventoryStock;
use Catalyst\InventoryBundle\Entity\InventoryEntry;
use Catalyst\InventoryBundle\Entity\InventoryTransaction;

use Catalyst\ValidationException;
use DateTime;

class DeliveryController extends BaseController
{
    protected function findPO($po_id)
    {
        $em = $this->getDoctrine()->getManager();

        // get the PO
        $po = $em->getRepository('CatalystPurchasingBundle:PurchaseOrder')->find($po_id);
        if ($po == null)
            throw new ValidationException('Cannot find purchase order.');

        return $po;
    }

    protected function findDelivery($id)
    {
        $em = $this->getDoctrine()->getManager();

        $deli = $em->getRepository('CatalystPurchasingBundle:PODelivery')->find($id);
        if ($deli == null)
            throw new ValidationException('Cannot find purchase order delivery.');

        return $deli;
    }

    public function indexAction($id)
    {
        $this->title = 'Purchase Order';
        $params = $this->getViewParams('Deliveries', 'cat_pur_del_index');

        $params['object'] = $this->findPO($id);

        return $this->render('CatalystPurchasingBundle:Delivery:index.html.twig', $params);
    }

    public function addFormAction($po_id)
    {
        $inv = $this->get('catalyst_inventory');
        $this->title = 'Purchase Order';
        $params = $this->getViewParams('Deliveries', 'cat_pur_del_index');

        $po = $this->findPO($po_id);

        $delivery = new PODelivery();
        $delivery->setPurchaseOrder($po);

        $params['object'] = $po;
        $params['delivery'] = $delivery;
        $params['wh_opts'] = $inv->getWarehouseOptions();

        return $this->render('CatalystPurchasingBundle:Delivery:add.html.twig', $params);
    }

    protected function update($delivery, $data, $update_entries = true)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');

        $delivery->setDateDeliver(new DateTime($data['date_deliver']))
            ->setCode($data['code']);

        if ($update_entries)
        {
            // clear all entries
            foreach ($delivery->getEntries() as $ent)
                $em->remove($ent);
            $delivery->clearEntries();

            // add entries
            foreach ($data['qty'] as $prod_id => $qty)
            {
                // no quantity, skip this product
                if ($qty <= 0)
                    continue;

                $prod = $inv->findProduct($prod_id);
                if ($prod == null)
                    throw new ValidationError('Could not find product.');

                $pode = new PODeliveryEntry();
                $pode->setQuantity($qty)
                    ->setProduct($prod);

                $delivery->addEntry($pode);
            }
        }

        $em->persist($delivery);
        $em->flush();
    }

    public function addSubmitAction($po_id)
    {
        $data = $this->getRequest()->request->all();
        $inv = $this->get('catalyst_inventory');
        $log = $this->get('catalyst_log');
        $po = $this->findPO($po_id);

        // find destination warehouse
        $dest_wh = $inv->findWarehouse($data['wh_id']);
        if ($dest_wh == null)
            throw new ValidationException('Could not find warehouse.');

        // get source warehouse
        $source_wh = $po->getSupplier()->getWarehouse();

        // setup inventory transaction
        $trans = $inv->newTransaction();
        $trans->setDescription('Delivery ' . $data['code'] . ' for PO ' . $po->getCode() . '.');
        $trans->setUser($this->getUser());

        // go through the product entries
        foreach ($data['qty'] as $prod_id => $qty)
        {
            // no quantity, skip this product
            if ($qty <= 0)
                continue;

            // find product
            $prod = $inv->findProduct($prod_id);
            if ($prod == null)
                throw new ValidationException('Could not find product.');

            // make source / supplier entry
            $source_entry = $inv->newEntry();
            $source_entry->setCredit($qty)
                ->setWarehouse($source_wh)
                ->setProduct($prod);
            $trans->addEntry($source_entry);

            // make destination entry
            $dest_entry = $inv->newEntry();
            $dest_entry->setDebit($qty)
                ->setWarehouse($dest_wh)
                ->setProduct($prod);
            $trans->addEntry($dest_entry);
        }

        // persist transaction
        $inv->persistTransaction($trans);

        // po delivery
        $delivery = new PODelivery();
        $delivery->setPurchaseOrder($po);
        $this->update($delivery, $data);

        // log
        $log->log('cat_inv_trans_add', 'added Inventory Transaction ' . $trans->getID() . '.', $trans->toData());
        $log->log('cat_pur_del_add', 'added Purchase Delivery ' . $delivery->getID() . '.', $delivery->toData());

        // flash
        $this->addFlash('success', 'Delivery successfuly added.');

        return $this->redirect($this->generateUrl('cat_pur_po_edit_form', array('id' => $po_id)));
    }

    public function editFormAction($id)
    {
        $this->title = 'Purchase Order';
        $params = $this->getViewParams('Deliveries', 'cat_pur_del_index');

        $delivery = $this->findDelivery($id);
        $po = $delivery->getPurchaseOrder();

        $inv = $this->get('catalyst_inventory');

        // build entries index
        $ent_index = array();
        foreach ($po->getEntries() as $entry)
            $ent_index[$entry->getProduct()->getID()]['delivered'] = 0.00;
        foreach ($delivery->getEntries() as $d_entry)
            $ent_index[$d_entry->getProduct()->getID()]['delivered'] = $d_entry->getQuantity();

        $params['object'] = $po;
        $params['delivery'] = $delivery;
        $params['ent_index'] = $ent_index;
        $params['wh_opts'] = $inv->getWarehouseOptions();

        return $this->render('CatalystPurchasingBundle:Delivery:edit.html.twig', $params);
    }

    public function editSubmitAction($id)
    {
        $log = $this->get('catalyst_log');
        $data = $this->getRequest()->request->all();
        $delivery = $this->findDelivery($id);
        $po_id = $delivery->getPurchaseOrder()->getID();

        $this->update($delivery, $data, false);

        $log->log('cat_pur_del_update', 'updated Purchase Delivery ' . $delivery->getID() . '.', $delivery->toData());

        $this->addFlash('success', 'Delivery successfuly edited.');

        return $this->redirect($this->generateUrl('cat_pur_po_edit_form', array('id' => $po_id)));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $delivery = $this->findDelivery($id);
        $po_id = $delivery->getPurchaseOrder()->getID();

        $em->remove($delivery);
        $em->flush();

        $this->addFlash('success', 'Delivery successfuly deleted.');

        return $this->redirect($this->generateUrl('cat_pur_po_edit_form', array('id' => $po_id)));
    }
}
