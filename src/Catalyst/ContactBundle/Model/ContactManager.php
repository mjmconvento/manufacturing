<?php

namespace Catalyst\ContactBundle\Model;

use Catalyst\ContactBundle\Entity\ContactType;
use Catalyst\ContactBundle\Entity\PhoneType;
use Doctrine\ORM\EntityManager;

class ContactManager
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * 
     * @param type $id
     * @return Catalyst/ContactBundle/Entity/PhoneType
     */
    public function getPhoneType($id)
    {
        return $this->em->getRepository('CatalystContactBundle:PhoneType')->find($id);
    }
    
    /**
     * 
     * @param type $id
     * @return Catalyst/ContactBundle/Entity/ContactType
     */
    public function getContactType($id)
    {
        return $this->em->getRepository('CatalystContactBundle:ContactType')->find($id);
    }


    public function getContactTypeOptions($filter = array())
    {
        $pgs = $this->em
            ->getRepository('CatalystContactBundle:ContactType')
            ->findBy(
                $filter,
                array('id' => 'ASC')
            );

        $pg_opts = array();
        foreach ($pgs as $pg)
            $pg_opts[$pg->getID()] = $pg->getName();

        return $pg_opts;
    }

    public function getPhoneTypeOptions($filter = array())
    {
        $pgs = $this->em
            ->getRepository('CatalystContactBundle:PhoneType')
            ->findBy(
                $filter,
                array('id' => 'ASC')
            );

        $pg_opts = array();
        foreach ($pgs as $pg)
            $pg_opts[$pg->getID()] = $pg->getName();

        return $pg_opts;
    }
}
