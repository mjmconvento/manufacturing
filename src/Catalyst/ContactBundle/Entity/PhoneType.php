<?php

namespace Catalyst\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;

/**
* @ORM\Entity
* @ORM\Table(name="cnt_phn_type")
*/
class PhoneType
{
	use HasGeneratedID;

	/** @ORM\Column(type="string", length=50) */
	protected $type;

	public function __construct()
	{
		$this->initHasGeneratedID();
	}

	public function setPhoneType($type)
	{
		$this->type = $type;
		return $this;
	}

	public function getPhoneType()
	{
		return $this->$type;
	}

	public function toData()
	{
		$data = new \stdClass();

		$this->dataHasGeneratedID();

		$data->type = $this->type;

		return $data;
	}
}