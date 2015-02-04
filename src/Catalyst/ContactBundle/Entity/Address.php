<?php

namespace Catalyst\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Catalyst\CoreBundle\Model\HasGeneratedID;
use Catalyst\CoreBundle\Model\TrackCreate;

/**
 * @ORM\Entity
 * @ORM\Table(name="cnt_address")
 */
class Address
{
    use HasGeneratedID;
    use TrackCreate;

    /** @ORM\Column(type="string", length=50) */
    protected $name;

    /** @ORM\Column(type="string", length=150) */
    protected $street;

    /** @ORM\Column(type="string", length=80) */
    protected $city;

    /** @ORM\Column(type="string", length=80) */
    protected $state;

    /** @ORM\Column(type="string", length=80) */
    protected $country;

    /** @ORM\Column(type="decimal", precision=10, scale=7) */
    protected $latitude;

    /** @ORM\Column(type="decimal", precision=10, scale=7) */
    protected $longitude;

    public function __construct()
    {
        $this->initializeTrackCreate();
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setLatitude($lat)
    {
        $this->latitude = $lat;
        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLongitude($long)
    {
        $this->longitude = $long;
        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function toData()
    {
        $data = new \stdClass();

        $data->id = $this->id;
        $data->name = $this->name;
        $data->street = $this->street;
        $data->city = $this->city;
        $data->state = $this->state;
        $data->country = $this->country;
        $data->latitude = $this->latitude;
        $data->longitude = $this->longitude;

        return $data;
    }
}
