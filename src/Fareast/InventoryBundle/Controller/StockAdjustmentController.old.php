<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\InventoryBundle\Entity\Warehouse;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\GridBundle\Model\Grid\Column as GCol;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockAdjustmentController extends BaseController
{
    public function indexAction()
    {
        $this->title = 'Stock Adjustment';
        $this->route_prefix = 'feac_inv_adjust';

        $params = $this->getViewParams('','feac_inv_adjust_index');

        $params['grid_cols'] = $this->getGridColumns();
        $em = $this->getDoctrine()->getManager();

        $inv = $this->get('catalyst_inventory');
        $params['dept'] = $inv->getWarehouseOptions();
        $params['prodgroup_opts'] = $inv->getProductGroupOptions();

        $stock = $em->getRepository('CatalystInventoryBundle:Stock')->findAll();

        // $data = array();
        // foreach($stock as $s)
        // {
        //     if($s->getQuantity() > 0)
        //     $data[] = [
        //         'code' => $s->getProduct()->getSKU(),
        //         'warehouse' => $s->getInventoryAccount()->getName(),
        //         'wh_id' => $s->getInventoryAccount()->getID(),
        //         'name' => $s->getProduct()->getName(),
        //         'prod_id' => $s->getProduct()->getID(),
        //         'quantity' =>$s->getQuantity(),
        //         'uom' => $s->getProduct()->getUnitOfMeasure(),
        //     ];
        // }

        // $params['data'] = $data;

        return $this->render('FareastInventoryBundle:StockAdjustment:index.html.twig', $params);
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Item Code', 'getSKU', 'sku', 'p'),
            $grid->newColumn('Warehouse','getWarehouse','w'),
            $grid->newColumn('Item Name', 'getName', 'name', 'p'),
            $grid->newColumn('Quantity', 'getQuantity', 'qty'),
            $grid->newColumn('Unit of Measure', 'getUnitOfMeasure', 'uom', 'p'),            
            $grid->newColumn('Actual Quantity','getID','dept','w',array($this,'formatQuantity'),false,false),
        );
    }

    public function formatQuantity($dept)
    {
        $number = "<input type='number' min='0' name='new_qty[{$dept}]'  class='form-control' />";
        return $number;
    }

    protected function setupGrid()
    {
        $grid = $this->get('catalyst_grid');
        $data = $this->getRequest()->query->all();
        $em = $this->getDoctrine()->getManager();

        // setup grid
        $gl = $grid->newLoader();
        $gl->processParams($data)
            ->setRepository('CatalystInventoryBundle:Stock')
            ->addJoin($grid->newJoin('p', 'product', 'getProduct'))
            ->addJoin($grid->newJoin('w', 'inv_account', 'getInventoryAccount'))
            ->enableCountFilter();
            // ->setQBFilterGroup($fg);

        // columns
        $stock_cols = $this->getGridColumns();
        foreach ($stock_cols as $col)
            $gl->addColumn($col);

        return $gl;
    }

    public function gridAction($whs = null, $cats = null)
    {
        $gl = $this->setupGrid();
        $qry = array();
        
        
        $grid = $this->get('catalyst_grid');
        $fg = $grid->newFilterGroup();


        // filter warehouses
        if($whs != null and $whs != 'null' )
        {
            $wh_ids = explode(',', $whs);
            $fg->andWhere('w.id IN (:wh_ids)')
                ->setParameter($wh_ids);

            /*
            $aw_id = explode(',',$dept);
            $qry[] = 'w.id IN ('. implode(', ', $aw_id).')';
            */
        }

        // prodgroup
        if ($cats != null and $cats != 'null' )
        {
            $cat_ids = explode(',', $cats);
            $fg->andWhere('p.prodgroup IN (:cat_ids)')
                ->setParameter($cat_ids);

            /*
            $aw_id = explode(',',$category);
            $qry[] = 'p.prodgroup IN ('. implode(', ', $aw_id).')';
            */
        }

        // $qry[] = 'o.quantity > 0';
        

        /*
        if (!empty($qry))
        {
            $filter = implode(' AND ', $qry);
            $fg->where($filter);
            $gl->setQBFilterGroup($fg);
        }
        */
        $gl->setQBFilterGroup($fg);

        $gres = $gl->load();

        $resp = new Response($gres->getJSON());
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
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
