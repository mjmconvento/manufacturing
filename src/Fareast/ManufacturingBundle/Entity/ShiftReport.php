<?php

namespace Fareast\ManufacturingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="mfg_shift_report")
 */
class ShiftReport
{
    use HasGeneratedID;
    use TrackCreate;

    /** @ORM\Column(type="date") */
    protected $date_produced;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $shift;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $fine_alcohol;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $heads_alcohol;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $alcohol_produced;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $ppt;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $proof;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $column_operator;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $beer_alcohol;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $fermentation;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $mixer;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $biogas_produced;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $gpla;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $biogas_bunker;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $biogas_steam;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $vfa;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $cod;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $slops;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $sampling;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $volume;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $temperature;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $ph;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $biogas_operator;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $husk;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $bunker;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $loa_lob;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $steam_produced;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $produced_steam;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $loa_husk;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $husk_loa;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $steam_loa;

    /** @ORM\Column(type="decimal" , precision=10, scale=2 , nullable=true) */
    protected $steam_husk;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $boiler;

    /** @ORM\Column(type="string" , length=40, nullable=true) */
    protected $boiler_operator;

    public function __construct()
    {
        $this->initTrackCreate();
    }

    public function setDateProduced(DateTime $date_produced)
    {
        $this->date_produced = $date_produced;
        return $this;
    }

    public function setShift($shift)
    {
        $this->shift = $shift;
        return $this;
    }

    public function setFineAlcohol($fine_alcohol)
    {
        $this->fine_alcohol = $fine_alcohol;
        return $this;
    }

    public function setHeadsAlcohol($heads_alcohol)
    {
        $this->heads_alcohol = $heads_alcohol;
        return $this;
    }

    public function setAlcoholProduced($alcohol_produced)
    {
        $this->alcohol_produced = $alcohol_produced;
        return $this;
    }

    public function setPPT($ppt)
    {
        $this->ppt = $ppt;
        return $this;
    }

    public function setPROOF($proof)
    {
        $this->proof = $proof;
        return $this;
    }

    public function setColumnOperator($column_operator)
    {
        $this->column_operator = $column_operator;
        return $this;
    }

    public function setBeerAlcohol($beer_alcohol)
    {
        $this->beer_alcohol = $beer_alcohol;
        return $this;
    }

    public function setFermentation($fermentation)
    {
        $this->fermentation = $fermentation;
        return $this;
    }

    public function setMixer($mixer)
    {
        $this->mixer = $mixer;
        return $this;
    }

    public function setBiogasProduced($biogas_produced)
    {
        $this->biogas_produced = $biogas_produced;
        return $this;
    }

    public function setGPLA($gpla)
    {
        $this->gpla = $gpla;
        return $this;
    }

    public function setBiogasBunker($biogas_bunker)
    {
        $this->biogas_bunker = $biogas_bunker;
        return $this;
    }

    public function setBiogasSteam($biogas_steam)
    {
        $this->biogas_steam = $biogas_steam;
        return $this;
    }

    public function setVFA($vfa)
    {
        $this->vfa = $vfa;
        return $this;
    }

    public function setCOD($cod)
    {
        $this->cod = $cod;
        return $this;
    }

    public function setSlops($slops)
    {
        $this->slops = $slops;
        return $this;
    }

    public function setSampling($sampling)
    {
        $this->sampling = $sampling;
        return $this;
    }

    public function setVolume($volume)
    {
        $this->volume = $volume;
        return $this;
    }

    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;
        return $this;
    }

    public function setPH($ph)
    {
        $this->ph = $ph;
        return $this;
    }

    public function setBiogasOperator($biogas_operator)
    {
        $this->biogas_operator = $biogas_operator;
        return $this;
    }

    public function setHusk($husk)
    {
        $this->husk = $husk;
        return $this;
    }

    public function setBunker($bunker)
    {
        $this->bunker = $bunker;
        return $this;
    }

    public function setLOALOB($loa_lob)
    {
        $this->loa_lob = $loa_lob;
        return $this;
    }

    public function setSteamProduced($steam_produced)
    {
        $this->steam_produced = $steam_produced;
        return $this;
    }

    public function setProducedSteam($produced_steam)
    {
        $this->produced_steam = $produced_steam;
        return $this;
    }

    public function setLOAHusk($loa_husk)
    {
        $this->loa_husk = $loa_husk;
        return $this;
    }

    public function setHuskLOA($husk_loa)
    {
        $this->husk_loa = $husk_loa;
        return $this;
    }

    public function setSteamLOA($steam_loa)
    {
        $this->steam_loa = $steam_loa;
        return $this;
    }

    public function setSteamHusk($steam_husk)
    {
        $this->steam_husk = $steam_husk;
        return $this;
    }

    public function setBoiler($boiler)
    {
        $this->boiler = $boiler;
        return $this;
    }

    public function setBoilerOperator($boiler_operator)
    {
        $this->boiler_operator = $boiler_operator;
        return $this;
    }

    public function getDateProduced()
    {
        return $this->date_produced;
    }

    public function getShift()
    {
        return $this->shift;
    }

    public function getFineAlcohol()
    {
        return $this->fine_alcohol;
    }

    public function getHeadsAlcohol()
    {
        return $this->heads_alcohol;
    }

    public function getAlcoholProduced()
    {
        return $this->alcohol_produced;
    }

    public function getPPT()
    {
        return $this->ppt;
    }

    public function getPROOF()
    {
        return $this->proof;
    }

    public function getColumnOperator()
    {
        return $this->column_operator;
    }

    public function getBeerAlcohol()
    {
        return $this->beer_alcohol;
    }

    public function getFermentation()
    {
        return $this->fermentation;
    }

    public function getMixer()
    {
        return $this->mixer;
    }

    public function getBiogasProduced()
    {
        return $this->biogas_produced;
    }

    public function getGPLA()
    {
        return $this->gpla;
    }

    public function getBiogasBunker()
    {
        return $this->biogas_bunker;
    }

    public function getBiogasSteam()
    {
        return $this->biogas_steam;
    }

    public function getVFA()
    {
        return $this->vfa;
    }

    public function getCOD()
    {
        return $this->cod;
    }

    public function getSlops()
    {
        return $this->slops;
    }

    public function getSampling()
    {
        return $this->sampling;
    }

    public function getVolume()
    {
        return $this->volume;
    }

    public function getTemperature()
    {
        return $this->temperature;
    }

    public function getPH()
    {
        return $this->ph;
    }

    public function getBiogasOperator()
    {
        return $this->biogas_operator;
    }

    public function getHusk()
    {
        return $this->husk;
    }

    public function getBunker()
    {
        return $this->bunker;
    }

    public function getLOALOB()
    {
        return $this->loa_lob;
    }

    public function getSteamProduced()
    {
        return $this->steam_produced;
    }

    public function getProducedSteam()
    {
        return $this->produced_steam;
    }

    public function getLOAHusk()
    {
        return $this->loa_husk;
    }

    public function getHuskLOA()
    {
        return $this->husk_loa;
    }

    public function getSteamLOA()
    {
        return $this->steam_loa;
    }

    public function getSteamHusk()
    {
        return $this->steam_husk;
    }

    public function getBoiler()
    {
        return $this->boiler;
    }

    public function getBoilerOperator()
    {
        return $this->boiler_operator;
    }

    // TODO : Update toData depending on the fields
    public function toData()
    {
        $data = new \stdClass();
        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);

        $data->entries = $entries;

        return $data;
    }
}
