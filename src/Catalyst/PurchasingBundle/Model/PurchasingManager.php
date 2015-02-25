<?php

namespace Catalyst\PurchasingBundle\Model;

use Catalyst\PurchasingBundle\Entity\Supplier;
use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
use Catalyst\PurchasingBundle\Entity\POEntry;
use Catalyst\PurchasingBundle\Entity\PurchaseRequest;
use Catalyst\PurchasingBundle\Entity\PREntry;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\InventoryBundle\Entity\ProductAttribute;
use Catalyst\InventoryBundle\Model\InventoryManager;
use DateTime;

use Doctrine\ORM\EntityManager;

class PurchasingManager
{
    protected $em;
    protected $user;

    public function __construct(EntityManager $em, $securityContext)
    {
        $this->em = $em;
        $this->user = $securityContext->getToken()->getUser();
    }
    
    public function newPurchaseOrder(){
        return new PurchaseOrder();
    }
    
    public function newPOEntry(){
        return new POEntry();
    }

    public function getSupplierOptions($filter = array())
    {
        $pgs = $this->em
            ->getRepository('CatalystPurchasingBundle:Supplier')
            ->findBy(
                $filter,
                array('id' => 'ASC')
            );

        $pg_opts = array();
        foreach ($pgs as $pg)
            $pg_opts[$pg->getID()] = $pg->getDisplayName();

        return $pg_opts;
    }
    
    public function getSupplier($id)
    {
        return $this->em->getRepository('CatalystPurchasingBundle:Supplier')->find($id);
    }
    public function getPurchaseRequest($id)
    {
        $pr = $this->em->getRepository('CatalystPurchasingBundle:PurchaseRequest')->find($id);
        if ($pr == null)
            throw new ValidationException('Cannot find purchase request.');
        return $pr;
    }
    
    public function getPurchaseOrder($id)
    {
        $po = $this->em->getRepository('CatalystPurchasingBundle:PurchaseOrder')->find($id);
        if ($po == null)
            throw new ValidationException('Cannot find purchase order.');
        return $po;
    }
   
    public function getDelivery($id)
    {
        $po = $this->em->getRepository('CatalystPurchasingBundle:PODelivery')->find($id);
        if ($po == null)
            throw new ValidationException('Cannot find delivery.');
        return $po;
    }
    
    public function findPurchaseOrderOptions($filter)
    {
        $po = $this->em->getRepository('CatalystPurchasingBundle:PurchaseOrder')
                ->findBy($filter, array('code' => 'ASC'));
        $po_opts = array();
        foreach ($po as $pg)
            $po_opts[$pg->getID()] = $pg->getCode();
        return $po_opts;
    }
    
    public function copytoPurchaseOrder(PurchaseRequest $pr){
        $po = $this->newPurchaseOrder();

        $po->setDateNeeded($pr->getDateNeeded());
        $po->setStatus(PurchaseOrder::STATUS_DRAFT);
        $po->setUserCreate($this->user);
        $po->setDateIssue(new DateTime());
        $po->setReferenceCode('');
                
        foreach($pr->getEntries() as $entry){
            $ent = $this->newPOEntry();
            $ent->setQuantity($entry->getQuantity());
            $ent->setProduct($entry->getProduct());
            $ent->setPrice($entry->getProduct()->getPricePurchase());
            
            $po->addEntry($ent);
        }
        $this->em->persist($po);
        $this->em->flush();
        $po->generateCode();
        $this->em->persist($po);
        $this->em->flush();
    }
    
    public function newProductWithExpiry($parentProd, $expiry){
        $inv = new InventoryManager($this->em);
        $attribute = new ProductAttribute();
        $exp = new DateTime($expiry);
        
        $attribute->setName('expiry');
        $attribute->setValue($expiry);
        
        return $inv->newVariant($parentProd,$attribute);
    }
    
    public function clearDeliveryEntries($delivery){
        foreach ($delivery->getEntries() as $ent){
              $this->em->remove($ent);
        }
        $delivery->clearEntries();
    }
    

     public function getQuantity($delivery, $product){
         
        $delivery->getEntries();
        $stock = $this->em->getRepository('CatalystInventoryBundle:Delivery')
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
    
    
    //Create or find a product with the variant or return the parent if there is no variant
    public function findProductWithExpiry($parentProd, $expiry){
        if($expiry != ''){
            $variant = $parentProd->getVariantsByAttribute('expiry', $expiry);
            if(count($variant) > 0){
                //Variant found
                $prodDelivery = $variant[0];
            }else {
                // New Variant
                $prodDelivery = $this->newProductWithExpiry($parentProd, $expiry);
            }
        }else {
            // No Variant
            $prodDelivery = $parentProd;
        }
        
        return $prodDelivery;
    }
    
}
