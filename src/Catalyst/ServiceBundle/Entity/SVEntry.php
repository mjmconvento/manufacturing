<?php

namespace Catalyst\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\UserBundle\Entity\User;
use DateTime;

/**
 * @ORM\Entity
 */
class SVEntry
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


    /** @ORM\Column(type="integer") */
    protected $assigned_id;

    /**
     * @ORM\ManyToOne(targetEntity="ServiceOrder")
     * @ORM\JoinColumn(name="so_id", referencedColumnName="id")
     */
    protected $service_order;

    /**
     * @ORM\OneToMany(targetEntity="SVETask", mappedBy="sve", cascade={"persist"})
     */
    protected $sve_tasks;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assigned_id", referencedColumnName="id")
     */
    protected $assigned_user;



    public function __construct()
    {
        $this->price = 0.00;
        $this->quantity = 0.00;
        $this->sve_tasks = new ArrayCollection();
        $this->assigned_user = null;
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

    public function setServiceOrder(ServiceOrder $so)
    {
        $this->service_order = $so;
        return $this;
    }

    public function setProduct(Product $prod)
    {
        $this->product = $prod;
        return $this;
    }

    public function setAssignedUser(User $user = null)
    {
        $this->assigned_user = $user;
        return $this;
    }

    public function addTask(SVETask $task)
    {
        // set parent entry
        $task->setSVEntry($this);

        // add task
        $this->sve_tasks->add($task);

        return $this;
    }

    public function clearTasks()
    {
        $this->sve_tasks->clear();
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

    public function getServiceOrder()
    {
        return $this->service_order;
    }

    public function getProductID()
    {
        return $this->product_id;
    }

    public function getProduct()
    {
        if ($this->product_id == null || $this->product_id == 0)
        {
            $prod = new Product();
            $prod->setName('Deleted Product')
                ->setFlagService();

            return $prod;
        }
        return $this->product;
    }

    public function getAssignedUser()
    {
        return $this->assigned_user;
    }

    public function getDateIssue()
    {
        return $this->service_order->getDateIssue();
    }

    public function getProductCount()
    {
        return 1;
    }

    public function getAssignedID()
    {
        if ($this->assigned_user == null)
            return null;

        return $this->assigned_user->getID();
    }

    public function getAssignedName()
    {
        if ($this->assigned_user == null)
            return null;

        return $this->assigned_user->getName();
    }


    public function getTasks()
    {
        return $this->sve_tasks;
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
