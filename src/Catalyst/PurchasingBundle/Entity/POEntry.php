<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;
use Catalyst\CoreBundle\Template\Entity\HasProduct;
use Catalyst\CoreBundle\Template\Entity\HasPrice;
use Catalyst\PurchasingBundle\Entity\PODelivery;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_po_entry")
 */
class POEntry
{
    use HasGeneratedID;
    use HasQuantity;
    use HasProduct;
    use HasPrice;

    /** @ORM\Column(type="integer") */
    protected $po_id;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseOrder")
     * @ORM\JoinColumn(name="po_id", referencedColumnName="id")
     */
    protected $purchase_order;

    public function __construct()
    {
        $this->initHasQuantity();
        $this->initHasPrice();
    }

    public function setPurchaseOrder(PurchaseOrder $po)
    {
        $this->purchase_order = $po;
        $this->po_id = $po->getID();
        return $this;
    }

    public function getPurchaseOrder()
    {
        return $this->purchase_order;
    }
    
    public function getDeliveredQuantity()
    {
        $deliveries = $this->getPurchaseOrder()->getDeliveries();
        $qty = 0;
        foreach($deliveries as $delivery){
            
            if($delivery->getStatus() == PODelivery::STATUS_RECEIVED){
                foreach($delivery->getEntries() as $delivery_entry){
                    if($delivery_entry->getProduct()->getRootProduct() === $this->getProduct()){
                        $qty += $delivery_entry->getQuantity();
                    } 
                }
            }
        }
        return $qty;
    }

    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataHasQuantity($data);
        $this->dataHasProduct($data);
        $this->dataHasPrice($data);
        $data->po_id = $this->getPurchaseOrder()->getID();
        return $data;
    }
}

