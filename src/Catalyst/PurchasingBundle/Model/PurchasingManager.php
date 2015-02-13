<?php

namespace Catalyst\PurchasingBundle\Model;

use Catalyst\PurchasingBundle\Entity\Supplier;
use Doctrine\ORM\EntityManager;

class PurchasingManager
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getSupplierOptions($filter = array())
    {
        $pgs = $this->em
            ->getRepository('CatalystPurchasingBundle:Supplier')
            ->findBy(
                $filter,
                array('id' => 'ASC')
            );

        $pg_opts = array();
        foreach ($pgs as $pg)
            $pg_opts[$pg->getID()] = $pg->getDisplayName();

        return $pg_opts;
    }
    
    public function getSupplier($id)
    {
        return $this->em->getRepository('CatalystPurchasingBundle:Supplier')->find($id);
    }

}
