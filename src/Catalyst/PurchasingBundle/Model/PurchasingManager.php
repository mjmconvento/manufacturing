<?php

namespace Catalyst\PurchasingBundle\Model;

use Catalyst\PurchasingBundle\Entity\Supplier;
use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
use Catalyst\PurchasingBundle\Entity\POEntry;
use Catalyst\PurchasingBundle\Entity\PurchaseRequest;
use Catalyst\PurchasingBundle\Entity\PREntry;
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
        return $this->em->getRepository('CatalystPurchasingBundle:PurchaseRequest')->find($id);
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
    

}
