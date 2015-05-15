<?php

namespace Catalyst\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\BorrowedTransaction;
use Catalyst\InventoryBundle\Entity\BIEntry;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\InventoryBundle\Entity\Entry;
use Catalyst\InventoryBundle\Entity\Stock;
use Catalyst\InventoryBundle\Entity\ReturnedItem;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\InventoryBundle\Entity\Product;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class BorrowedTransactionController extends CrudController
{
    use TrackCreate;
    public function __construct()
    {        
        $this->route_prefix = 'cat_inv_borrowed';
        $this->title = 'Borrowed Item';

        $this->list_title = 'Borrowed Items';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new BorrowedTransaction();
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getCode();
    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');        

        $twig_file = 'CatalystInventoryBundle:BorrowedTransaction:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        $date_from = new DateTime();
        $date_to = new DateTime();
        $date_from->format("Y-m-d");
        $date_to->format("Y-m-d");

        $date_from = new DateTime($date_from->format("Y-m-d")." 00:00:00");
        $date_to =  new DateTime($date_to->format("Y-m-d")." 23:59:59");
        

        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;

        //fetch the data based on date range
        $em = $this->getDoctrine()->getManager();
        $borrowed = $em->getRepository('CatalystInventoryBundle:BorrowedTransaction')->findAll();
        $params['data'] = $this->getBorrowCreated($date_from,$date_to);

        $this->padFormParams($params, $date_from, $date_to);



        return $this->render($twig_file, $params);
    }

    public function getDeptAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CatalystUserBundle:User')->findAll();

        //get the department of user
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
                    'prod_id' => $u->getID()
                ];
            }
        }

        return new JsonResponse($data);   
    }

    protected function padFormParams(&$params, $date_from = null, $date_to = null)
    {
        $em = $this->getDoctrine()->getManager();

        $um = $this->get('catalyst_user');  
        $user_opts = array(0 => '[Select User]');
        $params['user_opts'] = $user_opts + $um->getUserOptions();

        if ($date_from != null and $date_to != null)
        {    
            $date_from = $date_from->format("Y-m-d");
            $date_to = $date_to->format("Y-m-d");
        }
        else
        {
            $date_from = new DateTime();
            $date_to = new DateTime(); 
        }

        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;
        
        // get product options (fixed assets only)
        $products = $em->getRepository('CatalystInventoryBundle:Product')
            ->findBy(array('type_id' => Product::TYPE_FIXED_ASSET));
        $prod_opts = array();
        foreach ($products as $prod)
            $prod_opts[$prod->getID()] = $prod->getName();
        $params['prod_opts'] = $prod_opts;

        $params['status_opts'] = array(
            'Incomplete'=> BorrowedTransaction::STATUS_INCOMPLETE, 
            'Complete'=> BorrowedTransaction::STATUS_COMPLETE
        );        
        
        return $params;
    }

    public function filterAction($date_from, $date_to)
    {
        //filter for date range
        $params = $this->getViewParams('List');
        $params['list_title'] = $this->list_title;     
        $date_from = new DateTime($date_from);
        $date_to = new DateTime($date_to);
        $params['data'] = $this->getBorrowCreated($date_from,$date_to);
        $this->padFormParams($params, $date_from, $date_to);

        return $this->render('CatalystInventoryBundle:BorrowedTransaction:index.html.twig', $params);
    }

    public function getBorrowCreated($date_from, $date_to)
    {

        //filter by date range
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT b FROM CatalystInventoryBundle:BorrowedTransaction b
                    WHERE b.date_issue >= :date_from AND b.date_issue <= :date_to ');
        $query ->setParameter('date_from', $date_from)
               ->setParameter('date_to', $date_to);

        return $query->getResult();
    }

    protected function update($o, $data, $is_new = false)
    {
        //TODO: check if returned item is equals to number of borrowed
        //TODO: clean up the 90 percent of the code  
        //TODO: borrowed transaction adding when no new is found


        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die();
        
        
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('catalyst_user');
        $inv = $this->get('catalyst_inventory');

        //insert data to Borrowed Transaction Entity
        $o->setBorrower($user->findUser($data['user_opts']));
        $o->setDateIssue(new DateTime($data['date_issue']));
        
        $this->updateTrackCreate($o, $data, $is_new);
        $o->setStatus($data['status']);

        $o->setDescription($data['description']);
        $o->setRemark($data['remark']);

        // clear entries
        // $ents = $o->getEntries();
        // foreach($ents as $ent)
        //     $em->remove($ent);
        // $o->clearEntries();



        // checking if theres new borrowed entry
        $checker = false;
        if(isset($data['prod_opts']))
        {

            foreach ($data['prod_opts'] as $index => $prod_id) 
            {

                if(isset($data['is_new'][$index]))
                {
                    $checker = true;
                }

            }            
        }

        if ($checker == true)
        {
            // transaction
            $transaction = new Transaction();
            $transaction->setDescription('Borrowed Item')
                ->setDateCreate(new DateTime)
                ->setUserCreate($this->getUser());

            $em->persist($transaction);
        }



        //process each row (saved to BIEntry entity)
        if(isset($data['prod_opts']))
        {


            foreach ($data['prod_opts'] as $index => $prod_id) 
            {

                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);   

                $qty = $data['qty'][$index];  
                $id = $data['id'][$index];
                
                // main warehouse account
                $config = $this->get('catalyst_configuration');
                $main_warehouse = $config->get('catalyst_warehouse_main');
                $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($main_warehouse);
                $wh_acc = $wh->getInventoryAccount();

                // adjustment stock warehouse
                $adj_warehouse_id = $config->get('catalyst_warehouse_stock_adjustment');
                $adj_warehouse = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($adj_warehouse_id);
                $adj_acc = $adj_warehouse->getInventoryAccount();


                $entry = $em->getRepository('CatalystInventoryBundle:BIEntry')->find($id);
                if ($entry == null)
                {

                    // instantiate
                    $entry = new BIEntry();
                    $entry->setProduct($prod)
                        ->setQuantity($qty);

                    // add entry
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
                    $adj_entry->setInventoryAccount($o->getBorrower()->getDepartment()->getInventoryAccount())
                        ->setProduct($prod)
                        ->setDebit($qty)
                        ->setTransaction($transaction);

                    $em->persist($adj_entry);


                    // update borower account
                    $borrower_acc = $this->getUser()->getDepartment()->getInventoryAccount();
                    $dept_stock = $stock_repo->findOneBy(array('inv_account' => $borrower_acc, 'product' => $prod));
          
                    if ($dept_stock == null)
                    {
                        $dept_stock = new Stock($borrower_acc, $prod);
                    }
                        
                    $dept_stock_total = bcadd($dept_stock->getQuantity(), $qty , 2);
                    $dept_stock->setQuantity($dept_stock_total);
                    $em->persist($dept_stock);




                }
            }            
        }

        // main warehouse account
        $config = $this->get('catalyst_configuration');
        $main_warehouse = $config->get('catalyst_warehouse_main');
        $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($main_warehouse);
        $wh_acc = $wh->getInventoryAccount();


        if(isset($data['date_returned']))
        {
            // transaction
            $transaction = new Transaction();
            $transaction->setDescription('Returned Item')
                ->setDateCreate(new DateTime)
                ->setUserCreate($this->getUser());

            $em->persist($transaction);


            foreach ($data['date_returned'] as $index => $id) 
            {

                $entry = $em->getRepository('CatalystInventoryBundle:BIEntry')->find($data['entry_id'][$index]);
                $date_returned = new DateTime($data['date_returned'][$index]);
                $qty_returned = $data['qty_returned'][$index];

                $returned_item = new ReturnedItem;
                $returned_item->setBIEntry($entry)
                    ->setDateReturned($date_returned)
                    ->setQuantity($qty_returned);

                $em->persist($returned_item);
                
                // update stock
                $inv = $this->get('catalyst_inventory');
                // $old_qty = $inv->getStockCount($wh_acc, $prod);

                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($data['prod_id'][$index]); 

                $stock_repo = $em->getRepository('CatalystInventoryBundle:Stock');
                $stock_prod = $stock_repo->findOneBy(array('inv_account' => $wh_acc, 'product' => $prod));
                $old_qty = $stock_prod->getQuantity();

                $new_quantity = bcadd($old_qty, $qty_returned, 2);


                $stock = $stock_repo->findOneBy(array('inv_account' => $wh_acc, 'product' => $prod));

                $stock->setQuantity($new_quantity);
                $em->persist($stock);


                // update stock 2

                $borrower_acc = $this->getUser()->getDepartment()->getInventoryAccount();
                $dept_stock = $stock_repo->findOneBy(array('inv_account' => $borrower_acc, 'product' => $prod));
                                  
                $dept_stock_total = bcsub($dept_stock->getQuantity(), $qty_returned , 2);
                $dept_stock->setQuantity($dept_stock_total);
                $em->persist($dept_stock);



                // entry for adjustment
                $adj_entry = new Entry();
                $adj_entry->setInventoryAccount($o->getBorrower()->getDepartment()->getInventoryAccount())
                    ->setProduct($prod)
                    ->setCredit($qty_returned)
                    ->setTransaction($transaction);

                $em->persist($adj_entry);

                // entry for warehouse
                $wh_entry = new Entry();
                $wh_entry->setInventoryAccount($wh_acc)
                    ->setProduct($prod)
                    ->setDebit($qty_returned)
                    ->setTransaction($transaction);

                $em->persist($wh_entry);


            }
        }            
        


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

        $borrowed = $em->getRepository('CatalystInventoryBundle:BIEntry')->findBy(array('borrowed' => $id ));

        $data = array();
        foreach ($borrowed as $b) 
        {
            $data[] =[
                'name' => $b->getProduct()->getName(),
                'created' => $b->getBorrowed()->getDateIssue(),
                'quantity' => $b->getQuantity(),
                'create' => $b->getBorrowed()->getUserCreate(),
                'borrower' => $b->getBorrowed()->getBorrower()->getName(),
                'remark' => $b->getBorrowed()->getRemark(),
                'description' => $b->getBorrowed()->getDescription(),
                'code' => $b->getBorrowed()->getCode(),
            ];
        }

        $params['data'] = $data;

        $html = $this->render('CatalystInventoryBundle:BorrowedTransaction:print.html.twig', $params);
        return $pdf->printPdf($html->getContent());
    }

    public function getStockReport()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT o.code, d.name, LOWER(o.date_issue), o.status  
            FROM CatalystInventoryBundle:BorrowedTransaction o 
            INNER JOIN o.borrower u 
            INNER JOIN u.dept d');
        return $query->getResult();
    }
}
