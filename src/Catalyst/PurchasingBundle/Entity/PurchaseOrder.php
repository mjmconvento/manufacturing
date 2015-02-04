<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_purchase_order")
 */
class PurchaseOrder
{
    const STATUS_DRAFT      = 'draft';
    const STATUS_APPROVED   = 'approved';
    const STATUS_SENT       = 'sent';
    const STATUS_CANCEL     = 'cancelled';
    const STATUS_FULFILLED  = 'fulfilled';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string", length=40, nullable=false) */
    protected $code;

    /** @ORM\Column(type="datetime") */
    protected $date_in;

    /** @ORM\Column(type="date") */
    protected $date_issue;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $total_price;

    /** @ORM\Column(type="string", length=30) */
    protected $status_id;

    /** @ORM\Column(type="integer") */
    protected $supplier_id;

    /**
     * @ORM\ManyToOne(targetEntity="Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    protected $supplier;

    /**
     * @ORM\OneToMany(targetEntity="POEntry", mappedBy="purchase_order", cascade={"persist"})
     */
    protected $entries;

    /**
     * @ORM\OneToMany(targetEntity="PODelivery", mappedBy="purchase_order", cascade={"persist"})
     */
    protected $deliveries;


    public function __construct()
    {
        $this->date_in = new DateTime();
        $this->entries = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
        $this->total_price = 0.0;
        $this->status_id = 'draft';
    }

    // setters
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setDateIssue(DateTime $date)
    {
        $this->date_issue = $date;
        return $this;
    }

    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;
        return $this;
    }

    public function setSupplier(Supplier $supplier)
    {
        $this->supplier = $supplier;
        $this->supplier_id = $supplier->getID();
        return $this;
    }

    public function addEntry(POEntry $entry)
    {
        // set purchase order
        $entry->setPurchaseOrder($this);

        // add entry
        $this->entries->add($entry);

        // add total price
        $price = $entry->getPrice() * $entry->getQuantity();
        $this->total_price = bcadd($this->total_price, $price, 2);

        return $this;
    }

    public function clearEntries()
    {
        $this->entries->clear();
        $this->total_price = 0.0;
        return $this;
    }

    public function addDelivery(PODelivery $del)
    {
        $del->setPurchaseOrder($this);

        $this->deliveries->add($del);

        return $this;
    }

    public function clearDeliveries()
    {
        $this->deliveries->clear();
        return $this;
    }

    public function setStatus($status)
    {
        // TODO: check for invalid status
        $this->status_id = $status;
        return $this;
    }

    // getters
    public function getID()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getSupplierID()
    {
        return $this->supplier_id;
    }

    public function getDateCreate()
    {
        return $this->date_in;
    }

    public function getDateIssue()
    {
        return $this->date_issue;
    }

    public function getTotalPrice()
    {
        return $this->total_price;
    }

    public function getSupplier()
    {
        return $this->supplier;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function getDeliveries()
    {
        return $this->deliveries;
    }

    public function getStatus()
    {
        return $this->status_id;
    }

    public function getStatusFormatted()
    {
        return ucfirst($this->status_id);
    }

    public function getTotalSalesOrder()
    {
        $total = 0;
        foreach($this->entries as $entry)
        {
            $total += $entry->getQuantity();
        }
        return number_format($total, 2);
    }

    // utils
    public function canDeliver()
    {
        if ($this->status_id == self::STATUS_SENT)
            return true;

        if ($this->status_id == self::STATUS_FULFILLED)
            return true;

        return false;
    }

    public function canApprove()
    {
        if ($this->status_id == self::STATUS_DRAFT)
            return true;

        return false;
    }

    public function canSend()
    {
        if ($this->status_id == self::STATUS_DRAFT)
            return true;

        if ($this->status_id == self::STATUS_APPROVED)
            return true;

        return false;
    }
    
    public function canCancel()
    {
        if ($this->status_id == self::STATUS_DRAFT)
            return true;

        if ($this->status_id == self::STATUS_APPROVED)
            return true;

        return false;
    }

    public function canModifyEntries()
    {
        if ($this->status_id == self::STATUS_DRAFT)
            return true;

        return false;
    }

    public function canFulfill()
    {
        if ($this->status_id == self::STATUS_SENT)
            return true;

        return false;
    }

    public function canAddDelivery()
    {
        if ($this->status_id == self::STATUS_SENT)
            return true;

        return false;
    }

    public function toData()
    {
        $data = new \stdClass();

        $data->id = $this->id;
        $data->code = $this->code;
        $data->date_in = $this->date_in;
        $data->date_issue = $this->date_issue;
        $data->supplier_id = $this->supplier_id;
        $data->total_price = $this->total_price;
        $data->status_id = $this->status_id;

        $entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}
