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
    * @ORM\JoinColumn(name="phn_typ_id", referencedColumnName="id")
    */
    protected $name;

    /** @ORM\Column(type="string", length=50) */
    protected $number;

    public function __construct()
    {
        $this->initHasGeneratedID();
        $this->initTrackCreate();
    }

    public function setPhoneType(PhoneType $name)
    {
        $this->name = $name;
        $this->phn_typ_id= $name->getID();
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
    
    
    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);

        $data->phn_typ_id = $this->phn_typ_id;
        $data->number = $this->number;

        return $data;
    }
}
