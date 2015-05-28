<?php

namespace Fareast\ReceivingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\InventoryBundle\Template\Entity\HasProduct;
use DateTime;


/**
 * @ORM\Entity
 * @ORM\Table(name="rec_delivery_entry")
 */
class RODeliveryEntry
{
	use HasGeneratedID;
    use HasQuantity;
    use HasProduct;

    /**
     * @ORM\ManyToOne(targetEntity="ReceivedOrder")
     * @ORM\JoinColumn(name="rec_del_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $delivery;

    public function __construct()
    {
        $this->initHasQuantity();        
    }

    public function setDelivery(ReceivedOrder $delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    public function getDelivery()
    {
        return $this->delivery;
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