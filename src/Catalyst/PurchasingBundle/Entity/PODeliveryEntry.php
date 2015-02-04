<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\InventoryBundle\Entity\Product;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_po_delivery_entry")
 */
class PODeliveryEntry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="PODelivery")
     * @ORM\JoinColumn(name="podel_id", referencedColumnName="id")
     */
    protected $delivery;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    public function __construct()
    {
        $this->quantity = 0.00;
    }

    public function setQuantity($qty)
    {
        $this->quantity = $qty;
        return $this;
    }

    public function setDelivery(PODelivery $delivery)
    {
        $this->delivery = $delivery;
        return $this;
    }

    public function setProduct(Product $prod)
    {
        $this->product = $prod;
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getDelivery()
    {
        return $this->delivery;
    }

    public function getProduct()
    {
        return $this->product;
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
