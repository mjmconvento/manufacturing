<?php

namespace Catalyst\InventoryBundle\Model;

use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\InventoryBundle\Entity\ProductAttribute;
use Catalyst\InventoryBundle\Entity\Entry;
use Catalyst\InventoryBundle\Entity\Transaction;
use Catalyst\InventoryBundle\Entity\Stock;
use Catalyst\ConfigurationBundle\Model\ConfigurationManager;
use Doctrine\ORM\EntityManager;

class InventoryManager
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, $container = null)
    {
        $this->em = $em;
        $this->container = $container;
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

    public function findPColor($id)
    {
        return $this->em->getRepository('CatalystInventoryBundle:PColor')->find($id);
    }

    public function findPCondition($id)
    {
        return $this->em->getRepository('CatalystInventoryBundle:PCondition')->find($id);
    }

    public function findPMaterial($id)
    {
        return $this->em->getRepository('CatalystInventoryBundle:PMaterial')->find($id);
    }


    public function findProduct($id)
    {
        return $this->em->getRepository('CatalystInventoryBundle:Product')->find($id);
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
            if($prod->getSku() == "")
            {
                $prod_opts[$prod->getID()] = $prod->getName();
            }
            else
            {
                $prod_opts[$prod->getID()] = $prod->getSku() . ' - ' . $prod->getName();
            }
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
            if($prod->getSku() == "")
            {
                $prod_opts[$prod->getID()] = $prod->getName();
            }
            else
            {
                $prod_opts[$prod->getID()] = $prod->getSku() . ' - ' . $prod->getName();
            }
        return $prod_opts;
    }

    public function updateStock(Entry $entry)
    {
        // TODO: db row locking

        $wh = $entry->getWarehouse();
        $prod = $entry->getProduct();

        $qty = bcsub($entry->getDebit(), $entry->getCredit(), 2);

        // get stock
        $stock_repo = $this->em->getRepository('CatalystInventoryBundle:Stock');
        $stock = $stock_repo->findOneBy(array('warehouse' => $wh, 'product' => $prod));
        if ($stock == null)
        {
            $stock = new Stock();
            $stock->setWarehouse($wh)
                ->setProduct($prod)
                ->setQuantity($qty);

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
        // TODO: lock table

        // check balance
        if (!$trans->checkBalance())
            throw new InventoryException('Inventory transaction unbalanced. Incoming entries must be equivalent to outgoing entries.');

        // TODO: check product stock / availability in source warehouse

        // TODO: start doctrine transaction

        // persist transaction
        $this->em->persist($trans);

        // update inventory stock
        $entries = $trans->getEntries();
        foreach ($entries as $entry)
            $this->updateStock($entry);

        // TODO: end doctrine transaction

        // TODO: unlock table
    }
    
    public function newVariant(Product $parent, ProductAttribute $attribute)
    {
        $new = clone $parent;
        $new->addVariantAttribute($attribute);
        
        // TODO  This part should be moved somewhere else
        $value = str_replace('/', '', $attribute->getValue());
        
        $new->setSku($parent->getSKU().'-'.$value);
        $attribute->setProduct($new);
        $parent->addVariant($new);
        
        $this->em->persist($new);
        $this->em->persist($parent);
        $this->em->flush();
                
        return $new;
    }
    
    public function itemsIn($product, $quantity){
        $conf = new ConfigurationManager($this->container);
        $main_wh = $this->findWarehouse($conf->get('catalyst_warehouse_main'));
        $supplier_wh = $this->findWarehouse($conf->get('catalyst_warehouse_supplier_default'));
        
        return $this->itemTransfer($product, $quantity, $supplier_wh, $main_wh);
        
    }
    
    public function itemTransfer($product,$quantity,$from,$to){
        $entryDebit = $this->newEntry();
        $entryCredit = $this->newEntry();
        
        $entryCredit->setProduct($product)
                    ->setWarehouse($from)
                    ->setCredit($quantity)
                    ->setDebit(0);
        
        $entryDebit->setProduct($product)
                    ->setWarehouse($to)
                    ->setDebit($quantity)
                    ->setCredit(0);
        
        return [$entryCredit, $entryDebit];
    }
    
    public function getWarehouseStock($warehouse){
        $stock = $this->em->getRepository('CatalystInventoryBundle:InventoryStock')
                ->findByWarehouse($warehouse);
        
        return $stock;
    }
    
    public function getStock( $warehouse, $product){
        $stock = $this->em->getRepository('CatalystInventoryBundle:InventoryStock')
                ->findOneBy(array('product' => $product,
                            'warehouse' => $warehouse));

        if($product->getVariants() == null){
            if($stock == null || empty($stock)){
                return 0;
            }else {
                    return $stock->getQuantity();
            }
        }else {
            $qty = $stock->getQuantity();
            foreach ($product->getVariants() as $variant){
                $qty += $this->getStock($warehouse, $variant);
            }
            return $qty;
        }
    }
    
    
}
