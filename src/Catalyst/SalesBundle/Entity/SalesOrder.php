<?php

namespace Catalyst\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use Catalyst\UserBundle\Entity\User;
use Catalyst\InventoryBundle\Entity\Warehouse;

/**
 * @ORM\Entity
 * @ORM\Table(name="sale_sales_order")
 */
class SalesOrder
{
    const STATUS_DRAFT              = 'draft';
    const STATUS_APPROVE            = 'approve';
    const STATUS_CANCEL             = 'cancel';

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
    protected $customer_id;

    /** @ORM\Column(type="integer") */
    protected $warehouse_id;

    /** @ORM\Column(type="integer") */
    protected $user_id;

    /** @ORM\Column(type="integer") */
    protected $payment_id;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $tax;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $downpayment;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $balance;


    /**
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    protected $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="SOEntry", mappedBy="sales_order", cascade={"persist"})
     */
    protected $entries;

    /**
     * @ORM\ManyToOne(targetEntity="PaymentMethod")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    protected $payment_method;


    public function __construct()
    {
        $this->date_in = new DateTime();
        $this->entries = new ArrayCollection();
        $this->status_id = self::STATUS_DRAFT;
        $this->total_price = 0.00;
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

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
        $this->customer_id = $customer->getID();
        return $this;
    }

    public function setWarehouse(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
        $this->warehouse_id = $warehouse->getID();
        return $this;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->user_id = $user->getID();
        return $this;
    }

    public function setUserID($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }
    
    public function addEntry(SOEntry $entry)
    {
        // set sales order
        $entry->setSalesOrder($this);

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

    public function setStatus($status)
    {
        // TODO: check for invalid status
        $this->status_id = $status;
        return $this;
    }

    public function setTax($tax)
    {
        $this->tax = $tax;
        return $this;
    }

    public function setDownpayment($downpayment)
    {
        $this->downpayment = $downpayment;
        return $this;
    }

    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }   

    public function setPaymentMethod(PaymentMethod $pm)
    {
        $this->payment_method = $pm;
        $this->payment_id = $pm->getID();

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

    public function getCustomerID()
    {
        return $this->customer_id;
    }

    public function getWarehouseID()
    {
        return $this->warehouse_id;
    }

    public function getUserID()
    {
        return $this->user_id;
    }

    public function getDateCreate()
    {
        return $this->date_in;
    }

    public function getDateIssue()
    {
        return $this->date_issue;
    }

    public function getDateIssueText()
    {
        return $this->date_issue->format('m/d/Y');
    }

    public function getTotalPrice()
    {
        return $this->total_price;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }

    public function getUser()
    {
        return $this->user;
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

    public function getTax()
    {
        return $this->tax;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function getDownpayment()
    {
        return $this->downpayment;
    }

    public function getPaymentMethod()
    {
        return $this->payment_method;
    }


    public function canDraft()
    {
        return true;
    }

    public function canApprove()
    {
        if ($this->status_id == self::STATUS_DRAFT)
            return true;

        return false;
    }

    public function canCancel()
    {
        if ($this->status_id == self::STATUS_DRAFT)
            return true;

        return false;
    }

    public function canModifyEntries()
    {
        if ($this->status_id == self::STATUS_DRAFT)
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
        $data->total_price = $this->total_price;
        $data->status_id = $this->status_id;
        $data->customer_id = $this->customer_id;
        $data->warehouse_id = $this->warehouse_id;
        $data->user_id = $this->user_id;
        $data->payment_id = $this->payment_id;

        $entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}
