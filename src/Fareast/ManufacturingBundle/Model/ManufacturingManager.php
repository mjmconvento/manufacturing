<?php

namespace Fareast\ManufacturingBundle\Model;

use Catalyst\InventoryBundle\Entity\Entry;
use Catalyst\ConfigurationBundle\Model\ConfigurationManager;
use Doctrine\ORM\EntityManager;
use DateTime;

class ManufacturingManager
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findDailyConsumption($date)
    {
        $date = new DateTime($date);
        $consumption_repo = $this->em->getRepository('FareastManufacturingBundle:DailyConsumption');
        $data = $consumption_repo->findOneBy(array('date_produced' => $date));

        if ($data != null)
        {
            return $data;
        }
        else
        {
            return null;
        }
    }

    public function getShiftReports($date)
    {
        $shift_report_repo = $this->em->getRepository('FareastManufacturingBundle:ShiftReport');

        $shift_reports = $shift_report_repo->createQueryBuilder('o')
            ->where('o.date_produced = :date_produced')
            ->setMaxResults(3)
            ->setParameter('date_produced', $date)
            ->getQuery();

        return $shift_reports->getResult();
    }  

}
