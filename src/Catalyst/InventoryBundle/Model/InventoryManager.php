<?php

namespace Catalyst\InventoryBundle\Model;

use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\InventoryBundle\Entity\ProductAttribute;
use Catalyst\InventoryBundle\Entity\Entry;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\InventoryBundle\Entity\Stock;
use Catalyst\InventoryBundle\Entity\Account;
use Catalyst\ValidationException;
use Catalyst\ConfigurationBundle\Model\ConfigurationManager;
use Doctrine\ORM\EntityManager;

class InventoryManager
{
    protected $em;
    protected $container;
    protected $user;

    public function __construct(EntityManager $em, $container = null, $security = null)
    {
        $this->em = $em;
        $this->container = $container;
        $this->user = $security->getToken()->getUser();
    }

    public function newEntry()
    {
        $entry = new Entry();
        return $entry;
    }

    public function newTransaction()
    {
        $trans = new Transaction();
        return $trans;
    }

    public function findWarehouse($id)
    {
        return $this->em->getRepository('CatalystInventoryBundle:Warehouse')->find($id);
    }

    public function findProductGroup($id)
    {
        return $this->em->getRepository('CatalystInventoryBundle:ProductGroup')->find($id);
    }

    public function findBrand($id)
    {
        return $this->em->getRepository('CatalystInventoryBundle:Brand')->find($id);
    }

    public function findProduct($id)
    {
        return $this->em->getRepository('CatalystInventoryBundle:Product')->find($id);
    }

    public function findProductByName($name)
    {
        $product = $this->em->getRepository('CatalystInventoryBundle:Product')->findOneByName($name);
        
        if ($product != null)
        {
            return $product;
        }
        else
        {
            return null;
        }
    }

    public function findInventoryAccount($id)
    {
        return $this->em->getRepository('CatalystInventoryBundle:Account')->find($id);
    }
    
    public function getProductStock($wh_inv_acc, $product)
    {
        return $this->em->getRepository('CatalystInventoryBundle:Stock')->findOneBy(array(   
                    'inv_account' => $wh_inv_acc,
                    'product' => $product
                ));

    }

    public function getWarehouseOptions($filter = array())
    {
        $whs = $this->em
            ->getRepository('CatalystInventoryBundle:Warehouse')
            ->findBy(
                $filter,
                array('name' => 'ASC')
            );

        $wh_opts = array();
        foreach ($whs as $wh)
            $wh_opts[$wh->getID()] = $wh->getName();

        return $wh_opts;
    }

    public function getInventoryAccountWarehouseOptions($filter = array())
    {
        $whs = $this->em
            ->getRepository('CatalystInventoryBundle:Warehouse')
            ->findBy(
                $filter,
                array('name' => 'ASC')
            );

        $wh_opts = array();
        foreach ($whs as $wh)
            $wh_opts[$wh->getInventoryAccount()->getID()] = $wh->getName();

        return $wh_opts;
    }

    public function getInventoryAccountDepartmentOptions($filter = array())
    {
        $depts = $this->em
            ->getRepository('CatalystUserBundle:Department')
            ->findBy(
                $filter,
                array('name' => 'ASC')
            );

        $dept_opts = array();
        foreach($depts as $dept)
            $dept_opts[$dept->getInventoryAccount()->getID()] = $dept->getName();

        return $dept_opts;
    }

    public function getProductGroupOptions($filter = array())
    {
        $pgs = $this->em
            ->getRepository('CatalystInventoryBundle:ProductGroup')
            ->findBy(
                $filter,
                array('name' => 'ASC')
            );

        $pg_opts = array();
        foreach ($pgs as $pg)
            $pg_opts[$pg->getID()] = $pg->getName();

        return $pg_opts;
    }

    public function getProductVariantsOption($product){
        $products = $product->getVariants();
        $prod_opts = array();

        foreach ($products as $prod)
            $prod_opts[$prod->getID()] = $prod->getName();

        return $prod_opts;
    }
    
    public function getProductOptions($filter = array())
    {
        $filter = array_merge($filter, array('parent'=> null));
        $products = $this->em
            ->getRepository('CatalystInventoryBundle:Product')
            ->findBy(
                $filter,
                array('sku' => 'ASC')
            );

        $prod_opts = array();
        foreach ($products as $prod)
            $prod_opts[$prod->getID()] = $prod->getName();

        return $prod_opts;
    }

    public function updateStock(Entry $entry)
    {
        // TODO: db row locking

        $account = $entry->getInventoryAccount();
        $prod = $entry->getProduct();

        $qty = bcsub($entry->getDebit(), $entry->getCredit(), 2);

        // get stock
        $stock_repo = $this->em->getRepository('CatalystInventoryBundle:Stock');
        $stock = $stock_repo->findOneBy(array('inv_account' => $account, 'product' => $prod));
        if ($stock == null)
        {
            $stock = new Stock($account, $prod, $qty);

            // persist the new stock object
            $this->em->persist($stock);
        }
        else
        {
            // add quantity
            $old_qty = $stock->getQuantity();
            $new_qty = bcadd($qty, $old_qty, 2);
            $stock->setQuantity($new_qty);
        }
    }

    public function persistTransaction(Transaction $trans)
    {
        // check balance
        if (!$trans->checkBalance())
            throw new InventoryException('Inventory transaction unbalanced. Incoming entries must be equivalent to outgoing entries.');

        // TODO: lock table

        // TODO: check product stock / availability in source warehouse

        // TODO: start doctrine transaction

        // persist transaction
        $this->em->persist($trans);

        // update inventory stock
        $entries = $trans->getEntries();

        foreach ($entries as $entry)
        {
            $this->updateStock($entry);
        }

        // TODO: end doctrine transaction

        // TODO: unlock table
    }
    
    public function newVariant(Product $parent, ProductAttribute $attribute)
    {
        $new = clone $parent;
        $new->addVariantAttribute($attribute);
        
        // TODO  This part should be moved somewhere else
        $new->generateSku();
        $attribute->setProduct($new);
        $parent->addVariant($new);
        
        $this->em->persist($new);
        $this->em->persist($parent);
        $this->em->flush();
                
        return $new;
    }
    
    // TODO: remove this
    // inventory service should not be reliant on main warehouse config
    public function itemsIn($product, $quantity,$supplier)
    {
        $conf = new ConfigurationManager($this->container);
        $from = $this->findWarehouse($conf->get('catalyst_warehouse_main'))->getInventoryAccount();
        $to = $supplier->getInventoryAccount();
        
        return $this->itemTransfer($product, $quantity, $from, $to);
    }
    
    // TODO: remove this too. this can be done better
    public function itemTransfer($product,$quantity,$from,$to)
    {
        $entryDebit = $this->newEntry();
        $entryCredit = $this->newEntry();
        
        $entryCredit->setProduct($product)
                    ->setInventoryAccount($from)
                    ->setCredit($quantity)
                    ->setDebit(0);
        
        $entryDebit->setProduct($product)
                    ->setInventoryAccount($to)
                    ->setDebit($quantity)
                    ->setCredit(0);
        
        return [$entryCredit, $entryDebit];
    }
    
    public function getWarehouseStock($warehouse)
    {
        $stock = $this->em->getRepository('CatalystInventoryBundle:Stock')
                ->findByAccount($warehouse->getInventoryAccount());
        
        return $stock;
    }

    public function getStockCount($inv_account, $product)
    {
        $stock = $this->em->getRepository('CatalystInventoryBundle:Stock')->find([
            'product' => $product,
            'inv_account' => $inv_account
        ]);

        if ($stock == null)
            $quantity = 0.00;
        else
            $quantity = $stock->getQuantity();

        return $quantity;
    }


    /**
     * 
     * @param type $warehouse
     * @param type $product
     * @return int
     * Returns the total quantity of a product and its variants
     */
    public function getStock( $warehouse, $product)
    {
        $stock = $this->em->getRepository('CatalystInventoryBundle:Stock')
                ->findOneBy(array('product' => $product,
                            'inv_account' => $warehouse->getInventoryAccount()));

        if($product->getVariants() == null){
            if($stock == null || empty($stock)){
                return 0;
            }else {
                    return $stock->getQuantity();
            }
        }else {
            $qty = $stock==null?0:$stock->getQuantity();
            foreach ($product->getVariants() as $variant){
                $qty += $this->getStock($warehouse, $variant);
            }
            return $qty;
        }
    }
}
