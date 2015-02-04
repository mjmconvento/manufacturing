<?php

namespace Catalyst\ServiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\UserBundle\Entity\User;

/**
 * @ORM\Entity
 */
class SVETask
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", precision=13, scale=2)
     */
    protected $sell_price;

    /**
     * @ORM\Column(type="decimal", precision=13, scale=2)
     */
    protected $cost_price;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sve_id;

    /**
     * @ORM\Column(type="integer") 
     */
    protected $assigned_id;

    /**
     * @ORM\ManyToOne(targetEntity="SVEntry")
     * #ORM\JoinColumn(name="sve_id", referencedColumnName="id")
     */
    protected $sve;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assigned_id", referencedColumnName="id")
     */
    protected $assign_user;

    public function __construct()
    {
        $this->sell_price = 0.00;
        $this->cost_price = 0.00;
        $this->assigned_id = null;
        $this->assign_user = null;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setSellPrice($price)
    {
        $this->sell_price = $price;
        return $this;
    }

    public function setCostPrice($price)
    {
        $this->cost_price = $price;
        return $this;
    }

    public function setSVEntry(SVEntry $sv)
    {
        $this->sve = $sv;
        $this->sve_id = $sv->getID();
        return $this;
    }

    public function setAssignedUser(User $user = null)
    {
        $this->assign_user = $user;
        if ($user == null)
            $this->assigned_id = null;
        else
            $this->assigned_id = $user->getID();

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

    public function getSellPrice()
    {
        return $this->sell_price;
    }

    public function getCostPrice()
    {
        return $this->cost_price;
    }

    public function getSVEntry()
    {
        return $this->sve;
    }

    public function getAssignedUser()
    {
        return $this->assign_user;
    }

    public function getAssignedID()
    {
        return $this->assigned_id;
    }

    public function toData()
    {
        $data = new \stdClass();

        $data->id = $this->id;
        $data->name = $this->name;
        $data->sell_price = $this->sell_price;
        $data->cost_price = $this->cost_price;
        $data->sve_id = $this->sve_id;
        $data->assigned_id = $this->assigned_id;


        return $data;
    }
}
