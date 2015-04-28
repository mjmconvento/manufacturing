<?php

namespace Catalyst\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\CoreBundle\Template\Entity\HasName;

use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_department")
 */
class Department
{
	use HasGeneratedID;
	use TrackCreate;
	use HasName;

    // TODO: hasInventoryAccount trait

    public function __construct()
    {
        $this->initHasGeneratedID();
        $this->initTrackCreate();
        $this->initHasName();
    }

    public function toData()
    {
        $data = new stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);
        $this->dataHasName($data);

        return $data;
    }
}
