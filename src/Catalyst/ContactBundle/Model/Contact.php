<?php

namespace Catalyst\ContactBundle\Model;

use Doctrine\ORM\Mapping as ORM;

trait Contact
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

    protected function initializeContact()
    {
        $this->initializeHasAddresses();
        $this->initializeHasPhones();
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

    public function getDisplayName()
    {
        // TODO: figure out if company vs individual
        return $this->last_name . ', ' . $this->first_name;
    }

    public function dataContact($data)
    {
        $data->first_name = $this->first_name;
        $data->last_name = $this->last_name;
        $data->middle_name = $this->middle_name;
        $data->salutation = $this->salutation;
        $data->email = $this->email;

        $this->dataHasAddresses($data);
        $this->dataHasPHones($data);
    }
}
