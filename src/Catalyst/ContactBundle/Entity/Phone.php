<?php

namespace Catalyst\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Catalyst\CoreBundle\Model\HasGeneratedID;
use Catalyst\CoreBundle\Model\TrackCreate;

/**
 * @ORM\Entity
 * @ORM\Table(name="cnt_phone")
 */
class Phone
{
    use HasGeneratedID;
    use TrackCreate;

    /** @ORM\Column(type="string", length=50) */
    protected $name;

    /** @ORM\Column(type="string", length=50) */
    protected $number;

    public function __construct()
    {
        $this->initializeTrackCreate();
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
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

        $data->id = $this->id;
        $data->name = $this->name;
        $data->number = $this->number;

        return $data;
    }
}
