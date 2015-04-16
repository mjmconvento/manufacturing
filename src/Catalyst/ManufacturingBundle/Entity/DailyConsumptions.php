<?php

namespace Catalyst\ManufacturingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="man_daily_consumptions")
 */
class DailyConsumptions
{
    use HasGeneratedID;

    /** @ORM\Column(type="datetime", nullable=true) */
    protected $date_create;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mol_beginning_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mol_purchases;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mol_pumped_mdt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mol_running_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mol_pondo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mol_production_gal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mol_production_ton;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mol_tsai;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $mol_brix;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $bunker_beginning_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $bunker_purchase;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $bunker_consumption;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $bunker_running_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sul_beginning_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sul_purchase;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sul_consumption;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sul_running_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $soda_beginning_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $soda_purchase;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $soda_consumption;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $soda_running_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $urea_beginning_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $urea_purchase;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $urea_consumption;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $urea_running_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $salt_beginning_balance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $salt_purchase;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $salt_consumption;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $salt_running_balance;

    public function __construct()
    {
        
    }

    public function setDateCreate(DateTime $date_create)
    {
        $this->date_create = $date_create;
        return $this;
    }

    public function setMolBeginningBalance($mol_beginning_balance)
    {
        $this->mol_beginning_balance = $mol_beginning_balance;
        return $this;
    }

    public function setMolPurchases($mol_purchases)
    {
        $this->mol_purchases = $mol_purchases;
        return $this;
    }

    public function setMolPumpedMDT($mol_pumped_mdt)
    {
        $this->mol_pumped_mdt = $mol_pumped_mdt;
        return $this;
    }

    public function setMolRunningBalance($mol_running_balance)
    {
        $this->mol_running_balance = $mol_running_balance;
        return $this;
    }

    public function setMolPondo($mol_pondo)
    {
        $this->mol_pondo = $mol_pondo;
        return $this;
    }

    public function setMolProductionGal($mol_production_gal)
    {
        $this->mol_production_gal = $mol_production_gal;
        return $this;
    }

    public function setMolProductionTon($mol_production_ton)
    {
        $this->mol_production_ton = $mol_production_ton;
        return $this;
    }

    public function setMolTsai($mol_tsai)
    {
        $this->mol_tsai = $mol_tsai;
        return $this;
    }

    public function setMolBrix($mol_brix)
    {
        $this->mol_brix = $mol_brix;
        return $this;
    }

    public function setBunkerBeginningBalance($bunker_beginning_balance)
    {
        $this->bunker_beginning_balance = $bunker_beginning_balance;
        return $this;
    }

    public function setBunkerPurchase($bunker_purchase)
    {
        $this->bunker_purchase = $bunker_purchase;
        return $this;
    }

    public function setBunkerConsumption($bunker_consumption)
    {
        $this->bunker_consumption = $bunker_consumption;
        return $this;
    }

    public function setBunkerRunningBalance($bunker_running_balance)
    {
        $this->bunker_running_balance = $bunker_running_balance;
        return $this;
    }


    public function setSulBeginningBalance($sul_beginning_balance)
    {
        $this->sul_beginning_balance = $sul_beginning_balance;
        return $this;
    }

    public function setSulPurchase($sul_purchase)
    {
        $this->sul_purchase = $sul_purchase;
        return $this;
    }

    public function setSulConsumption($sul_consumption)
    {
        $this->sul_consumption = $sul_consumption;
        return $this;
    }

    public function setSulRunningBalance($sul_running_balance)
    {
        $this->sul_running_balance = $sul_running_balance;
        return $this;
    }


    public function setSodaBeginningBalance($soda_beginning_balance)
    {
        $this->soda_beginning_balance = $soda_beginning_balance;
        return $this;
    }

    public function setSodaPurchase($soda_purchase)
    {
        $this->soda_purchase = $soda_purchase;
        return $this;
    }

    public function setSodaConsumption($soda_consumption)
    {
        $this->soda_consumption = $soda_consumption;
        return $this;
    }

    public function setSodaRunningBalance($soda_running_balance)
    {
        $this->soda_running_balance = $soda_running_balance;
        return $this;
    }

    public function setUreaBeginningBalance($urea_beginning_balance)
    {
        $this->urea_beginning_balance = $urea_beginning_balance;
        return $this;
    }

    public function setUreaPurchase($urea_purchase)
    {
        $this->urea_purchase = $urea_purchase;
        return $this;
    }

    public function setUreaConsumption($urea_consumption)
    {
        $this->urea_consumption = $urea_consumption;
        return $this;
    }

    public function setUreaRunningBalance($urea_running_balance)
    {
        $this->urea_running_balance = $urea_running_balance;
        return $this;
    }

    public function setSaltBeginningBalance($salt_beginning_balance)
    {
        $this->salt_beginning_balance = $salt_beginning_balance;
        return $this;
    }

    public function setSaltPurchase($salt_purchase)
    {
        $this->salt_purchase = $salt_purchase;
        return $this;
    }

    public function setSaltConsumption($salt_consumption)
    {
        $this->salt_consumption = $salt_consumption;
        return $this;
    }

    public function setSaltRunningBalance($salt_running_balance)
    {
        $this->salt_running_balance = $salt_running_balance;
        return $this;
    }



    public function getMolBeginningBalance()
    {
        return $this->mol_beginning_balance;
    }

    public function getMolPurchases()
    {
        return $this->mol_purchases;
    }

    public function getMolPumpedMDT()
    {
        return $this->mol_pumped_mdt;
    }

    public function getMolRunningBalance()
    {
        return $this->mol_running_balance;
    }

    public function getMolPondo()
    {
        return $this->mol_pondo;
    }

    public function getMolProductionGal()
    {
        return $this->mol_production_gal;
    }

    public function getMolProductionTon()
    {
        return $this->mol_production_ton;
    }

    public function getMolTsai()
    {
        return $this->mol_tsai;
    }

    public function getMolBrix()
    {
        return $this->mol_brix;
    }

    public function getBunkerBeginningBalance()
    {
        return $this->bunker_beginning_balance;
    }

    public function getBunkerPurchase()
    {
        return $this->bunker_purchase;
    }

    public function getBunkerConsumption()
    {
        return $this->bunker_consumption;
    }

    public function getBunkerRunningBalance()
    {
        return $this->bunker_running_balance;
    }

    public function getSulBeginningBalance()
    {
        return $this->sul_beginning_balance;
    }

    public function getSulPurchase()
    {
        return $this->sul_purchase;
    }

    public function getSulConsumption()
    {
        return $this->sul_consumption;
    }

    public function getSulRunningBalance()
    {
        return $this->sul_running_balance;
    }

    public function getSodaBeginningBalance()
    {
        return $this->soda_beginning_balance;
    }

    public function getSodaPurchase()
    {
        return $this->soda_purchase;
    }

    public function getSodaConsumption()
    {
        return $this->soda_consumption;
    }

    public function getSodaRunningBalance()
    {
        return $this->soda_running_balance;
    }


    public function getUreaBeginningBalance()
    {
        return $this->urea_beginning_balance;
    }

    public function getUreaPurchase()
    {
        return $this->urea_purchase;
    }

    public function getUreaConsumption()
    {
        return $this->urea_consumption;
    }

    public function getUreaRunningBalance()
    {
        return $this->urea_running_balance;
    }


    public function getSaltBeginningBalance()
    {
        return $this->salt_beginning_balance;
    }

    public function getSaltPurchase()
    {
        return $this->salt_purchase;
    }

    public function getSaltConsumption()
    {
        return $this->salt_consumption;
    }

    public function getSaltRunningBalance()
    {
        return $this->salt_running_balance;
    }

    public function toData()
    {
        $data = new \stdClass();
        $this->dataHasGeneratedID($data);

        return $data;
    }



}
