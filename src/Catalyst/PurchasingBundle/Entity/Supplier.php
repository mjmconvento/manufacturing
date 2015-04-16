<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\InventoryBundle\Entity\Warehouse;

use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\ContactBundle\Template\Entity\HasContactInfo;
use Catalyst\InventoryBundle\Template\Entity\HasInventoryAccount;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_supplier")
 */
class Supplier
{
    use HasGeneratedID;
    use TrackCreate;
    use HasContactInfo;
    use HasInventoryAccount;

    public function __construct()
    {
        $this->initTrackCreate();
        $this->initHasContactInfo();
    }

    public function toData()
    {
        $data = new \stdClass();
        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);
        $this->dataHasContactInfo($data);
        $this->dataHasInventoryAccount($data);

        return $data;
    }
}

