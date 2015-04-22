<?php

namespace Catalyst\SalesBundle\Model;

use Catalyst\SalesBundle\Entity\Customer;
use Catalyst\SalesBundle\Entity\SalesOrder;
use Doctrine\ORM\EntityManager;

class SalesManager
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function newCustomer()
    {
        $cust = new Customer();
        return $cust;
    }

    public function newSalesOrder()
    {
        $so = new SalesOrder();
        return $so;
    }

    public function findCustomer($id)
    {
        return $this->em->getRepository('CatalystSalesBundle:Customer')->find($id);
    }

    public function findSalesOrder($id)
    {
        return $this->em->getRepository('CatalystSalesBundle:SalesOrder')->find($id);
    }

    public function getCustomerOptions($filter = array())
    {
        $custs = $this->em
            ->getRepository('CatalystSalesBundle:Customer')
            ->findBy(
                $filter,
                array('last_name' => 'ASC')
            );

        $c_opts = array();
        foreach ($custs as $cust)
            $c_opts[$cust->getID()] = $cust->getDisplayName();

        return $c_opts;
    }
}
