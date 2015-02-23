<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_product_group")
 */
class ProductGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /** @ORM\Column(type="string", length=50) */
    protected $code;

    public function __construct()
    {
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function toData()
    {
        $data = new stdClass();
        $data->id = $this->id;
        $data->name = $this->name;
        $data->code = $this->code;

        return $data;
    }
}

