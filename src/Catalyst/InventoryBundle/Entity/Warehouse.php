<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasName;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\ContactBundle\Template\Entity\HasAddress;
use Catalyst\ContactBundle\Template\Entity\HasPhones;
use Catalyst\InventoryBundle\Template\Entity\HasAccount;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_warehouse")
 */
class Warehouse
{
    use HasGeneratedID;
    use HasName;
    use TrackCreate;
    use HasAddress;
    use HasAccount;
    use HasPhones;

    /** @ORM\Column(type="string", length=25) */
    protected $internal_code;

    /** @ORM\Column(type="boolean") */
    protected $flag_threshold;

    /** @ORM\Column(type="boolean") */
    protected $flag_shopfront;

    public function __construct()
    {
        $this->initHasGeneratedID();
        $this->initHasName();
        $this->initTrackCreate();
        $this->initHasAddress();
        $this->initHasAccount();
        $this->initHasPhones();

        $this->flag_threshold = true;
        $this->flag_shopfront = false;
        $this->internal_code = '';
    }

    public function setInternalCode($code)
    {
        $this->internal_code = $code;
        return $this;
    }

    public function setFlagThreshold($flag = true)
    {
        $this->flag_threshold = $flag;
        return $this;
    }

    public function setFlagShopfront($flag = true)
    {
        $this->flag_shopfront = $flag;
        return $this;
    }

    public function getInternalCode()
    {
        return $this->internal_code;
    }

    public function canTrackThreshold()
    {
        return $this->flag_threshold;
    }

    public function isShopfront()
    {
        return $this->flag_shopfront;
    }

    public function isStocktrack()
    {
        return $this->flag_stocktrack;
    }

    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataHasName($data);
        $this->dataTrackCreate($data);
        $this->dataHasAddress($data);
        $this->dataHasAccount($data);
        $this->dataHasPhones($data);

        $data->internal_code = $this->internal_code;
        $data->flag_threshold = $this->flag_threshold;
        $data->flag_shopfront = $this->flag_shopfront;

        return $data;
    }
}

