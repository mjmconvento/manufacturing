<?php

namespace Fareast\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\IssuedItem;
use Catalyst\InventoryBundle\Entity\IIEntry;
use Catalyst\InventoryBundle\Entity\Entry;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\InventoryBundle\Entity\Stock;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use DateTime;

class IssuedItemController extends CrudController
{
    use TrackCreate;
	public function __construct()
    {        
        $this->route_prefix = 'feac_inv_issued';
        $this->title = 'Issued Item';

        $this->list_title = 'Issued Items';
        $this->list_type = 'dynamic';
        $this->repo = 'CatalystInventoryBundle:IssuedItem';
    }

    protected function newBaseClass()
    {
        return new IssuedItem();
    }

    protected function getObjectLabel($obj)
    {
    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'FareastInventoryBundle:IssuedItem:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        $em = $this->getDoctrine()->getManager();
        $issued = $em->getRepository('CatalystInventoryBundle:IssuedItem')->findAll();
        $data = array();
        foreach ($issued as $i) {
            $data[] =[
                'id' => $i->getID(),
                'code' => $i->getCode(),                
                'date_issue' => $i->getDateIssue(),
                'user_create' => $i->getUserCreate(),
                'count' => $i->getTotalItem(),
            ];
        }

        $params['data'] = $data;

        return $this->render($twig_file, $params);
    }

    protected function padFormParams(&$params, $po = null)
    {
        $em = $this->getDoctrine()->getManager();

        $um = $this->get('catalyst_user');
        $inv = $this->get('catalyst_inventory');
        $params['user_opts'] = $um->getUserOptions(); 
        $params['prod_opts'] = $inv->getProductOptions();
        
        return $params;
    }

    protected function update($o, $data, $is_new = false)
    {
        // echo "<pre>";
        // print_r($data);
        // // print_r($ctr);
        // // print_r($date);
        // echo "</pre>";
        // die();
        
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('catalyst_user');
        $inv = $this->get('catalyst_inventory');

        $o->setIssuedTo($user->findUser($data['user_opts']));
        $o->setDateIssue(new DateTime($data['date_issue']));        
        $this->updateTrackCreate($o, $data, $is_new);

        // clear entries
        // $ents = $o->getEntries();
        // foreach($ents as $ent)
        //     $em->remove($ent);
        // $o->clearEntries();


        if(isset($data['prod_opts']))
        {
            // TODO: return stock if deleted
            // TODO: check if stock is existing
            

            // transaction
            $transaction = new Transaction();
            $transaction->setDescription('Issued Item')
                ->setDateCreate(new DateTime)
                ->setUserCreate($this->getUser());

            $em->persist($transaction);


            foreach ($data['prod_opts'] as $index => $prod_id) 
            {
                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);

                $qty = $data['qty'][$index];
                $rmk = $data['remarks'][$index];
                $des = $data['desc'][$index];    
                $id = $data['id'][$index];    

                // main warehouse account
                $config = $this->get('catalyst_configuration');
                $main_warehouse = $config->get('catalyst_warehouse_main');
                $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($main_warehouse);
                $wh_acc = $wh->getInventoryAccount();

                // adijustment stock warehouse
                $adj_warehouse_id = $config->get('catalyst_warehouse_stock_adjustment');
                $adj_warehouse = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($adj_warehouse_id);
                $adj_acc = $adj_warehouse->getInventoryAccount();

                $entry = $em->getRepository('CatalystInventoryBundle:IIEntry')->find($id);

                if ($entry == null)
                {
                    // Issued Item entry
                    $entry = new IIEntry();
                    $entry->setProduct($prod)
                        ->setQuantity($qty)
                        ->setRemarks($rmk)
                        ->setDescription($des);

                    $o->addEntry($entry);

                    // update stock
                    $inv = $this->get('catalyst_inventory');
                    $old_qty = $inv->getStockCount($wh_acc, $prod);
                    $new_quantity = bcsub($old_qty, $qty, 2);

                    $stock_repo = $em->getRepository('CatalystInventoryBundle:Stock');
                    $stock = $stock_repo->findOneBy(array('inv_account' => $wh_acc, 'product' => $prod));

                    $stock->setQuantity($new_quantity);
                    $em->persist($stock);

                    // entry for warehouse
                    $wh_entry = new Entry();
                    $wh_entry->setInventoryAccount($wh_acc)
                        ->setProduct($prod)
                        ->setCredit($qty)
                        ->setTransaction($transaction);

                    $em->persist($wh_entry);

                    // entry for adjustment
                    $adj_entry = new Entry();
                    $adj_entry->setInventoryAccount($o->getIssuedTo()->getDepartment()->getInventoryAccount())
                        ->setProduct($prod)
                        ->setDebit($qty)
                        ->setTransaction($transaction);

                    $em->persist($adj_entry);
                }   
                else
                {
                    $entry->setProduct($prod)
                        ->setQuantity($qty)
                        ->setRemarks($rmk)
                        ->setDescription($des);

                    $em->persist($entry);
                }
            }
        }        
    }

    public function getDeptAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CatalystUserBundle:User')->findAll();

        $data = array();
        foreach($user as $u)
        {
            if($u->getID() == $id)
            {
                $data = [
                'dept' => $u->getDepartment()->getName()

                ];
            }
        }

        return new JsonResponse($data);   
    }

    public function getProductAction($prod_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CatalystInventoryBundle:Product')->findAll();

        $data = array();
        foreach($user as $u)
        {
            if($u->getID() == $prod_id)
            {
                $data = [
                    'uom' => $u->getUnitOfMeasure(),
                    'prod_id' => $u->getID(),
                ];
            }
        }

        return new JsonResponse($data);   
    }

    protected function hookPostSave($obj, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();
        if($is_new){
            $obj->setUserCreate($this->getUser());
            $obj->generateCode();
            $em->persist($obj);
            $em->flush();
        }
    }

    public function printPDFAction($id)
    {
        $pdf = $this->get('catalyst_pdf');
        $pdf->newPdf('page_receipt');

        $em = $this->getDoctrine()->getManager();

        $issued = $em->getRepository('CatalystInventoryBundle:IIEntry')->findBy(array('issued' => $id ));

        $data = array();
        foreach ($issued as $i) 
        {
            $data[] =[
                'name' => $i->getProduct()->getName(),
                'description' => $i->getDescription(),
                'remark' => $i->getRemarks(),
                'quantity' => $i->getQuantity(),
                'create' => $i->getIssued()->getUserCreate(),
                'created' => $i->getIssued()->getDateIssue(),
                'code' => $i->getIssued()->getCode(),
            ];
        }

        $params['data'] = $data;

        $html = $this->render('FareastInventoryBundle:IssuedItem:print.html.twig', $params);
        return $pdf->printPdf($html->getContent());
    }

}