<?php

namespace Catalyst\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;

/**
 * @ORM\Entity
 * @ORM\Table(name="cnt_phone")
 */
class Phone
{
    use HasGeneratedID;
    use TrackCreate;

    /** 
    * @ORM\ManyToOne(targetEntity="PhoneType")
    * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
    */
    protected $name;

    /** @ORM\Column(type="string", length=50) */
    protected $number;

    /** @ORM\Column(type="boolean") */
    protected $is_primary;

    public function __construct()
    {
        $this->initHasGeneratedID();
        $this->initTrackCreate();
        $this->is_primary = false;
    }

    public function setPhoneType(PhoneType $name)
    {
        $this->name = $name;
        $this->type_id = $name->getID();
        return $this;
    }

    public function getPhoneType()
    {
        return $this->name;
    }

    public function setNumber($num)
    {
        $this->number = $num;
        return $this;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setIsPrimary($pri = true)
    {
        $this->is_primary = $pri;
        return $this;
    }

    public function getIsPrimary()
    {
        return $this->is_primary;
    }

    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);

        $data->type = $this->getPhoneType()->getName();
        $data->number = $this->number;
        $data->is_primary = $this->is_primary;

        return $data;
    }
}
