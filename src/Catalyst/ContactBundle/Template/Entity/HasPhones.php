<?php

namespace Catalyst\ContactBundle\Template\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\ContactBundle\Entity\Phone;

trait HasPhones
{
    /** @ORM\ManyToMany(targetEntity="\Catalyst\ContactBundle\Entity\Phone") */
    protected $phones;

    public function initHasPhones()
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
    
    public function dataHasPhones($data)
    {
        $phones = array();
        foreach ($this->phones as $add)
        {
            $phones[] = $add->toData();
        }

        $data->phones = $phones;
    }
}
