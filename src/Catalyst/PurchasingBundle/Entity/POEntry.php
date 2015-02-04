<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\InventoryBundle\Entity\Product;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_po_entry")
 */
class POEntry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $quantity;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $price;

    /** @ORM\Column(type="integer") */
    protected $po_id;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseOrder")
     * @ORM\JoinColumn(name="po_id", referencedColumnName="id")
     */
    protected $purchase_order;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    public function __construct()
    {
        $this->price = 0.00;
        $this->quantity = 0.00;
    }

    public function setQuantity($qty)
    {
        $this->quantity = $qty;
        return $this;
    }

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    public function setPurchaseOrder(PurchaseOrder $po)
    {
        $this->purchase_order = $po;
        $this->po_id = $po->getID();
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

    public function getPrice()
    {
        return $this->price;
    }

    public function getPurchaseOrder()
    {
        return $this->purchase_order;
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
        $data->price = $this->price;
        $data->po_id = $this->getPurchaseOrder()->getID();
        $data->product_id = $this->getProduct()->getID();

        return $data;
    }
}

