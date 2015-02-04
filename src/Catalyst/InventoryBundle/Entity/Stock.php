<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_stock")
 */
class Stock
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
     * @ORM\Column(type="integer")
     */
    protected $product_id;

    /**
     * @ORM\ManyToOne(targetEntity="Warehouse")
     * #ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    protected $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * #ORM\JoinColumn(name="product_id", referencedColumnName="id")
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

    public function setWarehouse(Warehouse $wh)
    {
        $this->warehouse = $wh;
        return $this;
    }

    public function setProduct(Product $prod)
    {
        $this->product = $prod;
        $this->product_id = $prod->getID();
        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }

    public function getProduct()
    {
        return $this->product;
    }
}
