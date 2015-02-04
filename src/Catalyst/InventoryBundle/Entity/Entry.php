<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_entry")
 */
class Entry
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
    protected $credit;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $debit;

    /**
     * @ORM\ManyToOne(targetEntity="Transaction")
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id")
     */
    protected $transaction;

    /**
     * @ORM\ManyToOne(targetEntity="Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    protected $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    public function __construct()
    {
        $this->debit = 0;
        $this->credit = 0;
    }

    public function setCredit($qty)
    {
        $this->credit = $qty;
        return $this;
    }

    public function setDebit($qty)
    {
        $this->debit = $qty;
        return $this;
    }

    public function setTransaction(Transaction $trans)
    {
        $this->transaction = $trans;
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
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getCredit()
    {
        return $this->credit;
    }

    public function getDebit()
    {
        return $this->debit;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function toData()
    {
        $data = new \stdClass();

        $data->id = $this->id;
        $data->credit = $this->credit;
        $data->debit = $this->debit;
        $data->transaction_id = $this->getTransaction()->getID();
        $data->warehouse_id = $this->getWarehouse()->getID();
        $data->product_id = $this->getProduct()->getID();

        return $data;
    }
}
