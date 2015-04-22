<?php

namespace Catalyst\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\InventoryBundle\Entity\Warehouse;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\SalesBundle\Entity\Customer;
use Catalyst\UserBundle\Entity\User;
use DateTime;

/**
 * @ORM\Entity
 */
class ServiceOrder
{
    const STATUS_ISSUE      = 'issued';
    const STATUS_RECEIVE    = 'received';
    const STATUS_ASSIGN     = 'assigned';
    const STATUS_SERVICE    = 'servicing';
    const STATUS_FINISH     = 'finished';
    const STATUS_CLAIM      = 'claimed';
    const STATUS_CANCEL     = 'cancelled';
    const STATUS_REFUND     = 'refund';

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
    protected $date_need;

    /** @ORM\Column(type="date") */
    protected $date_need_repairman;

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
    protected $product_id;

    /** @ORM\Column(type="integer") */
    protected $user_id;

    /** @ORM\Column(type="datetime") */
    protected $date_claimed;

    /** @ORM\Column(type="datetime") */
    protected $date_completed;    

    /** @ORM\Column(type="string") */
    protected $note;

    /** @ORM\Column(type="json_array") */
    protected $data;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\SalesBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    protected $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ORM\OneToMany(targetEntity="SVEntry", mappedBy="service_order", cascade={"persist"})
     */
    protected $entries;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    public function __construct()
    {
        $this->date_in = new DateTime();
        $this->date_need = new DateTime();
        $this->entries = new ArrayCollection();
        $this->total_price = 0.0;
        $this->status_id = self::STATUS_ISSUE;
        $this->data = array();
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

    public function setDateNeed(DateTime $date)
    {
        $this->date_need = $date;
        return $this;
    }

    public function setDateNeedRepairman(DateTime $date)
    {
        $this->date_need_repairman = $date;
        return $this;
    }

    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;
        return $this;
    }

    public function setCustomer(\Catalyst\SalesBundle\Entity\Customer $customer)
    {
        $this->customer = $customer;
        $this->customer_id = $customer->getID();
        return $this;
    }

    public function setWarehouse(Warehouse $wh)
    {
        $this->warehouse = $wh;
        $this->warehouse_id = $wh->getID();
        return $this;
    }

    public function setProduct(Product $prod)
    {
        $this->product = $prod;
        $this->product_id = $prod->getID();
        return $this;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        if ($user == null)
            $this->user_id = null;
        else
            $this->user_id = $user->getID();

        return $this;
    }

    public function setUserID($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }


    public function addEntry(SVEntry $entry)
    {
        // set service order
        $entry->setServiceOrder($this);

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

        if ($status == "finished")
        {
            $this->setDateCompleted(new DateTime());
        }
        if ($status == "claimed")
        {
            $this->setDateClaimed(new DateTime());
        }

        return $this;
    }

    public function setStatus2($status)
    {
        // TODO: check for invalid status
        $this->status_id = $status;
        return $this;
    }


    public function setDateClaimed(DateTime $date)
    {
        $this->date_claimed = $date;
        return $this;
    }

    public function setDateCompleted(DateTime $date)
    {
        $this->date_completed = $date;
        return $this;
    }

    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
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

    public function getDateCreate()
    {
        return $this->date_in;
    }

    public function getDateIssue()
    {
        return $this->date_issue;
    }

    public function getDateIssueFormat()
    {
        return $this->date_issue->format('m/d/Y');
    }

    public function getDateNeed()
    {
        return $this->date_need;
    }

    public function getDateNeedFormat()
    {
        return $this->date_need->format('m/d/Y');
    }

    public function getDateNeedRepairman()
    {
        return $this->date_need_repairman;
    }

    public function getDateNeedRepairmanFormat()
    {
        return $this->date_need_repairman->format('m/d/Y');
    }


    public function getTotalPrice()
    {
        return $this->total_price;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getWarehouseID()
    {
        return $this->warehouse_id;
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }

    public function getProductID()
    {
        return $this->product_id;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUserID()
    {
        return $this->user_id;
    }

    public function getEntries()
    {
        return $this->entries;
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

    public function getProductCount()
    {
        return 1;
    }

    public function getDateClaimed()
    {
        return $this->date_claimed;
    }

    public function getDateClaimedFormat()
    {
        if ($this->date_claimed == null)
        {
            return "";            
        }
        else
        {
            return $this->date_claimed->format('m/d/Y');
        }
    }

    public function getDateCompleted()
    {
        return $this->date_completed;
    }

    public function getDateCompletedFormat()
    {
        if ($this->date_completed == null)
        {
            return "";            
        }
        else
        {
            return $this->date_completed->format('m/d/Y');
        }

    }

    public function getNote()
    {
        return $this->note;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getAssignedUsersText()
    {
        $users = [];
        foreach ($this->entries as $entry)
        {
            $ea_user = $entry->getAssignedUser();
            if ($ea_user == null)
                continue;

            $users[] = $ea_user->getName();
        }

        return implode(', ', $users);
    }

    public function getPrices()
    {
        $price = [];
        foreach ($this->entries as $entry)
        {
            $price[] = $entry->getPrice();
        }

        return implode(', ', $price);
    }


    public function getServices()
    {
        $name = [];
        foreach ($this->entries as $entry)
        {
            $name[] = $entry->getProduct()->getCode()." - ".$entry->getProduct()->getName();
        }

        return implode(', ', $name);
    }



    // can utils
    public function canReceive()
    {
        if ($this->status_id == self::STATUS_ISSUE)
            return true;

        return false;
    }

    public function canAssign()
    {
        if ($this->status_id == self::STATUS_RECEIVE)
            return true;

        return false;
    }

    public function canService()
    {
        if ($this->status_id == self::STATUS_ASSIGN)
            return true;

        return false;
    }

    public function canFinish()
    {
        if ($this->status_id == self::STATUS_SERVICE)
            return true;

        return false;
    }

    public function canReturn()
    {
        if ($this->status_id == self::STATUS_FINISH)
            return true;

        return false;
    }

    public function canCancel()
    {
        if ($this->status_id == self::STATUS_ISSUE)
            return true;

        return false;
    }

    public function canEdit()
    {
        if ($this->status_id == self::STATUS_ISSUE)
            return true;

        return false;
    }

    public function canModifyEntries()
    {
        if ($this->status_id == self::STATUS_ISSUE)
            return true;

        if ($this->status_id == self::STATUS_RECEIVE)
            return true;

        return false;
    }

    public function getDeposit()
    {
        if (!isset($this->data['dp_amount']))
            return 0.00;

        return $this->data['dp_amount'];
    }

    public function getBalance()
    {
        if (!isset($this->data['bal_amount']))
            return 0.00;

        return $this->data['bal_amount'];
    }

    public function toData()
    {
        $data = new \stdClass();

        $data->id = $this->id;
        $data->code = $this->code;
        $data->date_in = $this->date_in;
        $data->date_need = $this->date_need;
        $data->date_issue = $this->date_issue;
        $data->date_need = $this->date_need;
        $data->date_need_repairman = $this->date_need_repairman;
        $data->total_price = $this->total_price;
        $data->status_id = $this->status_id;
        $data->customer_id = $this->customer_id;
        $data->warehouse_id = $this->warehouse_id;
        $data->product_id = $this->product_id;
        $data->user_id = $this->user_id;
        $data->date_claimed = $this->date_claimed;
        $data->date_completed = $this->date_completed;
        $data->note = $this->note;
        $data->data = $this->data;

        $entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}
