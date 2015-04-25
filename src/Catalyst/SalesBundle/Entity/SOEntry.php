<?php

namespace Catalyst\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\InventoryBundle\Entity\Product;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="sale_sales_order_entry")
 */
class SOEntry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $quantity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $price;

    /** @ORM\Column(type="integer") */
    protected $so_id;

    /** @ORM\Column(type="integer") */
    protected $product_id;

    /**
     * @ORM\ManyToOne(targetEntity="SalesOrder")
     * @ORM\JoinColumn(name="so_id", referencedColumnName="id")
     */
    protected $sales_order;

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

    public function setSalesOrder(SalesOrder $so)
    {
        $this->sales_order = $so;
        $this->so_id = $so->getID();
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

    public function getSalesOrder()
    {
        return $this->sales_order;
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
        $data->so_id = $this->so_id;
        $data->product_id = $this->product_id;

        return $data;
    }
}

