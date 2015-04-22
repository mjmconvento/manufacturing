<?php

namespace Catalyst\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\InventoryBundle\Entity\Warehouse;

use Catalyst\ContactBundle\Template\Entity\HasContactInfo;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;


/**
 * @ORM\Entity
 * @ORM\Table(name="sale_customer")
 */
class Customer
{
    use HasGeneratedID;
    use TrackCreate;
    use HasContactInfo;

    public function __construct()
    {
        $this->initHasGeneratedID();
        $this->initTrackCreate();
        $this->initHasContactInfo();
    }

    public function toData()
    {
        $data = new \stdClass();
        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);
        $this->dataHasContactInfo($data);

        return $data;
    }
}

