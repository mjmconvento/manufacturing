<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\InventoryBundle\Entity\Entry;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\ValidationException;
use Catalyst\InventoryBundle\Model\InventoryException;


class TransferProductionController extends CrudController
{

    public function __construct()
    {
        $this->route_prefix = 'feac_inv_transfer_production';
        $this->title = 'Transfer for Production';

        $this->list_title = 'Transfer for Production';
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
        $twig_file = 'FareastInventoryBundle:TransferProduction:index.html.twig';

        $inv = $this->get('catalyst_inventory');

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();
        $params['wh_opts'] = $inv->getWarehouseOptions();
        $params['main_warehouse_id'] = $this->getMainWarehouseID();
        $params['production_warehouse_id'] = $this->getProductionWarehouseID();
        $params['prodgroup_opts'] = $inv->getProductGroupOptions();  
        // $params['prod_opts'] = $inv->getProductGroupOptions();  

        return $this->render($twig_file, $params);
    }

    protected function getMainWarehouseID()
    {
        $em = $this->getDoctrine()->getManager();

        $config = $this->get('catalyst_configuration');
        $main_warehouse_id = $config->get('catalyst_warehouse_main');

        return $main_warehouse_id;
    }

    protected function getProductionWarehouseID()
    {
        $em = $this->getDoctrine()->getManager();

        $config = $this->get('catalyst_configuration');
        $production_warehouse_id = $config->get('catalyst_warehouse_production_tank');

        return $production_warehouse_id;
    }

    protected function processTransferEntries($data, $prefix)
    {
        $em = $this->getDoctrine()->getManager();

        // figure out setter
        if ($prefix == 'from')
            $setter = 'setCredit';
        else
            $setter = 'setDebit';

        // initialize entries
        $entries = array();

        $wh_id = $data[$prefix . '_wh_id'];

        // process it
        foreach ($data['prod_id'] as $index => $prod_id)
        {
            $qty = $data['qty'][$index];

            // product
            $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
            if ($prod == null)
                throw new ValidationException('Could not find product.');

            // warehouse
            $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($wh_id);
            if ($wh == null)
                throw new ValidationException('Could not find warehouse.');

            $entry = new Entry();
            $entry->setInventoryAccount($wh->getInventoryAccount())
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
            $log->log('feac_inv_transfer_production_index', 'added Inventory Transaction ' . $trans->getID() . '.', $odata);
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
        return $this->redirect($this->generateUrl('feac_inv_transfer_production_index'));
    }


}
