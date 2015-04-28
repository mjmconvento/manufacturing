<?php

namespace Catalyst\CoreBundle\Template\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Catalyst\UserBundle\Entity\User;

trait TrackUpdate
{
    /** @ORM\Column(type="datetime") */
    protected $date_update;

    /** 
     * @ORM\ManyToOne(targetEntity="\Catalyst\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_update_id", referencedColumnName="id")
     */
    protected $user_update;

    public function initTrackUpdate()
    {
        $this->date_update = new DateTime();
    }

    public function setDateUpdate(DateTime $date)
    {
        $this->date_update = $date;
        return $this;
    }

    public function getDateUpdate()
    {
        return $this->date_update;
    }

    public function setUserUpdate(User $user)
    {
        $this->user_update = $user;
        return $this;
    }

    public function getUserUpdate()
    {
        return $this->user_update;
    }

    protected function dataTrackUpdate(&$data)
    {
        $data->date_update = $this->date_update->format('Y-m-d H:i:s');
        $data->user_update = $this->user_update->toData();
    }
}
