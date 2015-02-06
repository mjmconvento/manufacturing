<?php

namespace Catalyst\ContactBundle\Template\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\ContactBundle\Entity\Address;

trait HasAddresses
{
    /** @ORM\ManyToMany(targetEntity="\Catalyst\ContactBundle\Entity\Address") */
    protected $addresses;

    protected function initializeHasAddresses()
    {
        $this->addresses = new ArrayCollection();
    }

    public function addAddress(Address $address)
    {
        $this->addresses->add($address);
        return $this;
    }

    public function clearAddresses()
    {
        $this->addresses->clear();
        return $this;
    }

    public function removeAddress(Address $address)
    {
        $this->addresses->removeElement($address);
        return $this;
    }

    public function getAddresses()
    {
        return $this->addresses;
    }

    public function dataHasAddresses($data)
    {
        $addresses = array();
        foreach ($this->addresses as $add)
        {
            $addresses[] = $add->toData();
        }

        $data->addresses = $addresses;
    }
}
