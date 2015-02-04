<?php

namespace Catalyst\ContactBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\ContactBundle\Entity\Phone;

trait HasPhones
{
    /** @ORM\ManyToMany(targetEntity="\Catalyst\ContactBundle\Entity\Phone") */
    protected $phones;

    public function initializeHasPhones()
    {
        $this->phones = new ArrayCollection();
    }

    public function addPhone(Phone $phone)
    {
        $this->phones->add($phone);
        return $this;
    }

    public function clearPhones()
    {
        $this->phones->clear();
        return $this;
    }

    public function removePhone(Phone $phone)
    {
        $this->phones->removeElement($phone);
        return $this;
    }

    public function getPhones()
    {
        return $this->phones;
    }
}
