<?php

namespace Catalyst\ContactBundle\Template\Entity;

use Doctrine\ORM\Mapping as ORM;

use Catalyst\ContactBundle\Entity\ContactType;

trait HasContactInfo
{
    use HasAddresses;
    use HasPhones;

    /** @ORM\Column(type="string", length=80) */
    protected $first_name;

    /** @ORM\Column(type="string", length=80) */
    protected $last_name;

    /** @ORM\Column(type="string", length=80) */
    protected $middle_name;

    /** @ORM\Column(type="string", length=80) */
    protected $salutation;

    /** @ORM\Column(type="string", length=80) */
    protected $email;

    /** 
    * @ORM\ManyToOne(targetEntity="\Catalyst\ContactBundle\Entity\ContactType")
    * @ORM\JoinColumn(name="cnt_type_id", referencedColumnName="id")
    */
    protected $contact_type;

    protected function initHasContactInfo()
    {
        $this->initHasAddresses();
        $this->initHasPhones();
    }

    public function setFirstName($name)
    {
        $this->first_name = $name;
        return $this;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function setLastName($name)
    {
        $this->last_name = $name;
        return $this;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function setMiddleName($name)
    {
        $this->middle_name = $name;
        return $this;
    }

    public function getMiddleName()
    {
        return $this->middle_name;
    }

    public function setSalutation($sal)
    {
        $this->salutation = $sal;
        return $this;
    }

    public function getSalutation()
    {
        return $this->salutation;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setContactTypeID(ContactType $c_type)
    {
        $this->contact_type = $c_type;
        return $this;
    }

    public function getContactTypeID()
    {
        return $this->contact_type;
    }

    public function getDisplayName()
    {
        // TODO: figure out if company vs individual
        return $this->last_name . ', ' . $this->first_name;
    }

    public function dataHasContactInfo($data)
    {
        $data->first_name = $this->first_name;
        $data->last_name = $this->last_name;
        $data->middle_name = $this->middle_name;
        $data->salutation = $this->salutation;
        $data->email = $this->email;
        $data->cnt_type_id = $this->getContactType()->getID();

        $this->dataHasAddresses($data);
        $this->dataHasPHones($data);
    }
}
