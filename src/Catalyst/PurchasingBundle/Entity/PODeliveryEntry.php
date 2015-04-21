<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\InventoryBundle\Template\Entity\HasProduct;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_po_delivery_entry")
 */
class PODeliveryEntry
{
    use HasGeneratedID;
    use HasQuantity;
    use HasProduct;
    /**
     * @ORM\ManyToOne(targetEntity="PODelivery")
     * @ORM\JoinColumn(name="podel_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $delivery;


    public function __construct()
    {
        $this->initHasQuantity();
    }

    public function setDelivery(PODelivery $delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    public function getDelivery()
    {
        return $this->delivery;
    }
    
    public function getExpiry()
    {
        $date = new DateTime($this->product->getAttributeValue('expiry'));
        return $date;
    }

    public function toData()
    {
        $data = new \stdClass();

        $data->id = $this->id;
        $data->quantity = $this->quantity;
        $data->delivery_id = $this->getDelivery()->getID();
        $data->product_id = $this->getProduct()->getID();

        return $data;
    }
}
