<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_warehouse")
 */
class Warehouse
{
    const TYPE_VIRTUAL          = 'virtual';
    const TYPE_PHYSICAL         = 'physical';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    

    /** @ORM\Column(type="string", length=25) */
    protected $internal_code;

    /** @ORM\Column(type="string", length=50) */
    protected $name;

    /** @ORM\Column(type="string", length=20) */
    protected $type_id;

    /** @ORM\Column(type="string", length=200) */
    protected $address;

    /** @ORM\Column(type="string", length=50) */
    protected $contact_num;

    /** @ORM\Column(type="boolean") */
    protected $flag_threshold;

    /** @ORM\Column(type="boolean") */
    protected $flag_shopfront;

    /** @ORM\Column(type="boolean") */
    protected $flag_stocktrack;

    public function __construct()
    {
        $this->type_id = 'physical';
        $this->flag_threshold = 0;
        $this->flag_shopfront = 0;
        $this->internal_code = '';
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setInternalCode($code)
    {
        $this->internal_code = $code;
        return $this;
    }

    public function setType($type_id)
    {
        $this->type_id = $type_id;
        return $this;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function setContactNumber($contact_num)
    {
        $this->contact_num = $contact_num;
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

    public function setFlagStocktrack($flag = true)
    {
        $this->flag_stocktrack = $flag;
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getInternalCode()
    {
        return $this->internal_code;
    }

    public function getContactnumber()
    {
        return $this->contact_num;
    }    

    public function getType()
    {
        return $this->type_id;
    }

    public function getTypeFormatted()
    {
        return ucfirst($this->type_id);
    }
    
    public function isVirtual()
    {
        if ($this->type_id == self::TYPE_VIRTUAL)
            return true;

        return false;
    }

    public function isPhysical()
    {
        if ($this->type_id == self::TYPE_PHYSICAL)
            return true;

        return false;
    }

    public function getAddress()
    {
        return $this->address;
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
        $data = new stdClass();
        $data->id = $this->id;
        $data->name = $this->name;
        $data->type_id = $this->type_id;
        $data->address = $this->address;
        $data->contact_num = $this->contact_num;
        $data->flag_threshold = $this->flag_threshold;
        $data->flag_shopfront = $this->flag_shopfront;

        return $data;
    }
}

