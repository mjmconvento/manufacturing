<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\InventoryBundle\Entity\Entry;

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

    public function getProductAndStockAction($prod_id, $wh_id)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        
        $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);

        if ($prod == null)
        {
            $json = [
                'prodtype' => '',
                'uom' => '',
                'current_stock' => 0.00
            ];
        }
        else
        {
            $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($wh_id);
            $iacc = $wh->getInventoryAccount();

            $quantity = $inv->getStockCount($iacc, $prod);

            $json = [
                'prodtype' => $prod->getTypeText(),
                'uom' => $prod->getUnitOfMeasure(),
                'current_stock' => $quantity
            ];
        }

        return new JsonResponse($json);   
    }

    public function getGroupAction($var_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $pg = $em->getRepository('CatalystInventoryBundle:ProductGroup')->find($var_id);
        $prods = $pg->getProducts();

        $data = array();
        foreach($prods as $p)
        {
            $data[] = [
                'name' => $p->getName(),
                'id' => $p->getID(),
            ];
        }

        return new JsonResponse($data);
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
