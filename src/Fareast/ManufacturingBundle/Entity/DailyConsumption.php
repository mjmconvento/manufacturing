<?php

namespace Fareast\ManufacturingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\CoreBundle\Template\Entity\TrackUpdate;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="mfg_daily_consumption")
 */
class DailyConsumption
{
    const PROD_MOLLASES             = "Mollases";
    const PROD_BUNKER               = "Bunker";
    const PROD_SULFURIC_ACID        = "Sulfuric Acid";
    const PROD_CAUSTIC_SODA         = "Caustic Soda"; 
    const PROD_UREA                 = "Urea";
    const PROD_SALT                 = "Salt";

    use HasGeneratedID;
    use TrackCreate;
    use TrackUpdate;

    /** @ORM\Column(type="date") */
    protected $date_produced;

    /** @ORM\Column(type="boolean", nullable=true) */
    protected $generated;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $mol_beginning_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $mol_purchases;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $mol_pumped_mdt;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $mol_running_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $mol_pondo;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $mol_production_gal;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $mol_production_ton;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $mol_tsai;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $mol_brix;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $bunker_beginning_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $bunker_purchase;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $bunker_consumption;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $bunker_running_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $sul_beginning_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $sul_purchase;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $sul_consumption;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $sul_running_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $soda_beginning_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $soda_purchase;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $soda_consumption;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $soda_running_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $urea_beginning_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $urea_purchase;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $urea_consumption;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $urea_running_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $salt_beginning_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $salt_purchase;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $salt_consumption;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $salt_running_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true, options={"default":"0.00"})
     */
    protected $electricity_final;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $electricity_beginning;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $electricity_used;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $ku_loa;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $fermentation_efficiency;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $distillation_efficiency;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $overall_efficiency;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $average_alcohol;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $alcohol_beginning_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $alcohol_out;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $aldehyde_beginning_balance;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $aldehyde_out;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $direct_labor_no;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $maintenance_no;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $support_group_no;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $plant_managers_no;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $guard_no;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $extra_no;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $direct_labor_mh;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $maintenance_mh;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $support_group_mh;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $plant_managers_mh;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $guard_mh;

    /**
     * @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true)
     */
    protected $extra_mh;

    public function __construct()
    {        
        $this->mol_beginning_balance = 0;
        $this->mol_purchases = 0;
        $this->mol_pumped_mdt = 0;
        $this->mol_running_balance = 0;
        $this->mol_pondo = 0;
        $this->mol_production_gal = 0;
        $this->mol_production_ton = 0;
        $this->mol_tsai = 0;
        $this->mol_brix = 0;
        $this->bunker_beginning_balance = 0;
        $this->bunker_purchase = 0;
        $this->bunker_consumption = 0;
        $this->bunker_running_balance = 0;
        $this->sul_beginning_balance = 0;
        $this->sul_purchase = 0;
        $this->sul_consumption = 0;
        $this->sul_running_balance = 0;
        $this->soda_beginning_balance = 0;
        $this->soda_purchase = 0;
        $this->soda_consumption = 0;
        $this->soda_running_balance = 0;
        $this->urea_beginning_balance = 0;
        $this->urea_purchase = 0;
        $this->urea_purchase = 0;
        $this->urea_consumption = 0;
        $this->urea_running_balance = 0;
        $this->salt_beginning_balance = 0;
        $this->salt_purchase = 0;
        $this->salt_purchase = 0;
        $this->salt_consumption = 0;
        $this->salt_running_balance = 0;
        
    }

    public function setDateProduced(DateTime $date_produced)
    {
        $this->date_produced = $date_produced;
        return $this;
    }

    public function setIsGenerated($flag = false)
    {
        $this->generated = $flag;
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

    public function setElectricityFinal($electricity_final)
    {
        $this->electricity_final = $electricity_final;
        return $this;
    }

    public function setElectricityBeginning($electricity_beginning)
    {
        $this->electricity_beginning = $electricity_beginning;
        return $this;
    }

    public function setElectricityUsed($electricity_used)
    {
        $this->electricity_used = $electricity_used;
        return $this;
    }

    public function setKuLOA($ku_loa)
    {
        $this->ku_loa = $ku_loa;
        return $this;
    }

    public function setFermentationEfficiency($fermentation_efficiency)
    {
        $this->fermentation_efficiency = $fermentation_efficiency;
        return $this;
    }

    public function setDistillationEfficiency($distillation_efficiency)
    {
        $this->distillation_efficiency = $distillation_efficiency;
        return $this;
    }

    public function setOverallEfficiency($overall_efficiency)
    {
        $this->overall_efficiency = $overall_efficiency;
        return $this;
    }

    public function setAverageAlcohol($average_alcohol)
    {
        $this->average_alcohol = $average_alcohol;
        return $this;
    }

    public function setAlcoholBeginning($alcohol_beginning_balance)
    {
        $this->alcohol_beginning_balance = $alcohol_beginning_balance;
        return $this;
    }

    public function setAlcoholOut($alcohol_out)
    {
        $this->alcohol_out = $alcohol_out;
        return $this;
    }

    public function setAldehydeBeginning($aldehyde_beginning_balance)
    {
        $this->aldehyde_beginning_balance = $aldehyde_beginning_balance;
        return $this;
    }

    public function setAldehydeOut($aldehyde_out)
    {
        $this->aldehyde_out = $aldehyde_out;
        return $this;
    }

    public function setDirectLaborNo($direct_labor_no)
    {
        $this->direct_labor_no = $direct_labor_no;
        return $this;
    }

    public function setMaintenanceNo($maintenance_no)
    {
        $this->maintenance_no = $maintenance_no;
        return $this;
    }

    public function setSupportGroupNo($support_group_no)
    {
        $this->support_group_no = $support_group_no;
        return $this;
    }

    public function setPlantManagersNo($plant_managers_no)
    {
        $this->plant_managers_no = $plant_managers_no;
        return $this;
    }

    public function setGuardNo($guard_no)
    {
        $this->guard_no = $guard_no;
        return $this;
    }

    public function setExtraNo($extra_no)
    {
        $this->extra_no = $extra_no;
        return $this;
    }



    public function setDirectLaborMH($direct_labor_mh)
    {
        $this->direct_labor_mh = $direct_labor_mh;
        return $this;
    }

    public function setMaintenanceMH($maintenance_mh)
    {
        $this->maintenance_mh = $maintenance_mh;
        return $this;
    }

    public function setSupportGroupMH($support_group_mh)
    {
        $this->support_group_mh = $support_group_mh;
        return $this;
    }

    public function setPlantManagersMH($plant_managers_mh)
    {
        $this->plant_managers_mh = $plant_managers_mh;
        return $this;
    }

    public function setGuardMH($guard_mh)
    {
        $this->guard_mh = $guard_mh;
        return $this;
    }

    public function setExtraMH($extra_mh)
    {
        $this->extra_mh = $extra_mh;
        return $this;
    }

    public function getDateProduced()
    {
        return $this->date_produced;
    }

    public function getIsGenerated()
    {
        return $this->generated;
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

    public function getElectricityFinal()
    {
        return $this->electricity_final;
    }

    public function getElectricityBeginning()
    {
        return $this->electricity_beginning;
    }

    public function getElectricityUsed()
    {
        return $this->electricity_used;
    }

    public function getKuLOA()
    {
        return $this->ku_loa;
    }

    public function getFermentationEfficiency()
    {
        return $this->fermentation_efficiency;
    }

    public function getDistillationEfficiency()
    {
        return $this->distillation_efficiency;
    }

    public function getOverallEfficiency()
    {
        return $this->overall_efficiency;
    }

    public function getAverageAlcohol()
    {
        return $this->overall_efficiency;
    }

    public function getAlcoholBeginning()
    {
        return $this->alcohol_beginning_balance;
    }

    public function getAlcoholOut()
    {
        return $this->alcohol_out;
    }

    public function getAldehydeBeginning()
    {
        return $this->aldehyde_beginning_balance;
    }

    public function getAldehydeOut()
    {
        return $this->aldehyde_out;
    }


    public function getDirectLaborNo()
    {
        return $this->direct_labor_no;
    }

    public function getMaintenanceNo()
    {
        return $this->maintenance_no;
    }

    public function getSupportGroupNo()
    {
        return $this->support_group_no;
    }

    public function getPlantManagersNo()
    {
        return $this->plant_managers_no;
    }

    public function getGuardNo()
    {
        return $this->guard_no;
    }

    public function getExtraNo()
    {
        return $this->extra_no;
    }

    public function getDirectLaborMH()
    {
        return $this->direct_labor_mh;
    }

    public function getMaintenanceMH()
    {
        return $this->maintenance_mh;
    }

    public function getSupportGroupMH()
    {
        return $this->support_group_mh;
    }

    public function getPlantManagersMH()
    {
        return $this->plant_managers_mh;
    }

    public function getGuardMH()
    {
        return $this->guard_mh;
    }

    public function getExtraMH()
    {
        return $this->extra_mh;
    }

    // TODO : Update toData depending on the fields
    public function toData()
    {
        $data = new \stdClass();
        $this->dataHasGeneratedID($data);

        return $data;
    }



}
