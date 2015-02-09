<?php

namespace Catalysy\TemplateBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Catalyst\ContactBundle\Entity\PhoneType;
use Catalyst\ContactBundle\Entity\ContactType;


class LoadContactTypes implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $types = ['Work', 'Mobile', 'Home', 'Fax'];
        foreach($types as $type){
            $pt = new PhoneType();
            $pt->setName($type);
            $em->persist($pt);
        }
        $em->flush();
    
    
        $types = ['Individual', 'Company'];
        foreach($types as $type){
            $ct = new ContactType();
            $ct->setName($type);
            $em->persist($ct);
        }
        $em->flush();    
    }
}