<?php

namespace Catalyst\CoreBundle\Template\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\UserBundle\Entity\Department;

trait HasDepartment
{
    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\UserBundle\Entity\Department")
     * @ORM\JoinColumn(name="dept_id", referencedColumnName="id")
     */
    protected $dept;

    public function setDepartment(Department $dept)
    {
        $this->dept = $dept;
        return $this;
    }

    public function getDepartment()
    {
        return $this->dept;
    }


    public function dataHasDepartment($data)
    {
        $data->dept = $this->dept->toData();
    }
}
