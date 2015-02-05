<?php

namespace Catalyst\SalesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\InventoryBundle\Entity\Warehouse;

use Catalyst\ContactBundle\Model\Contact;
use Catalyst\CoreBundle\Model\HasGeneratedID;
use Catalyst\CoreBundle\Model\TrackCreate;


/**
 * @ORM\Entity
 * @ORM\Table(name="sale_customer")
 */
class Customer
{
    use HasGeneratedID;
    use TrackCreate;
    use Contact;

    public function __construct()
    {
        $this->initializeTrackCreate();
        $this->initializeContact();
    }

    public function toData()
    {
        $data = new \stdClass();
        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);
        $this->dataContact($data);

        return $data;
    }
}

