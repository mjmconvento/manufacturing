<?php

namespace Catalyst\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\InventoryBundle\Entity\Warehouse;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="sale_customer")
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="datetime") */
    protected $date_in;

    /** @ORM\Column(type="string", length=80, nullable=false) */
    protected $name;

    /** @ORM\Column(type="string", length=150) */
    protected $address;

    /** @ORM\Column(type="string", length=80) */
    protected $contact_number;

    /** @ORM\Column(type="string", length=80) */
    protected $email;

    /** @ORM\Column(type="string", length=80) */
    protected $contact_person;

    /** @ORM\Column(type="text") */
    protected $notes;

    /** @ORM\Column(type="integer") */
    protected $warehouse_id;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    protected $warehouse;



    public function __construct()
    {
        $this->date_in = new DateTime();
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function setContactNumber($num)
    {
        $this->contact_number = $num;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setContactPerson($contact)
    {
        $this->contact_person = $contact;
        return $this;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    public function setWarehouse(Warehouse $wh)
    {
        $this->warehouse = $wh;
        $this->warehouse_id = $wh->getID();
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getDateCreate()
    {
        return $this->date_in;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getContactNumber()
    {
        return $this->contact_number;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getContactPerson()
    {
        return $this->contact_person;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }

    public function getWarehouseID()
    {
        return $this->warehouse_id;
    }

    public function toData()
    {
        $data = new \stdClass();

        $data->id = $this->id;
        $data->name = $this->name;
        $data->date_in = $this->date_in;
        $data->address = $this->address;
        $data->contact_number = $this->contact_number;
        $data->email = $this->email;
        $data->contact_person = $this->contact_person;
        $data->notes = $this->notes;
        $data->warehouse_id = $this->warehouse_id;

        return $data;
    }
}

