<?php

namespace Catalyst\TemplateBundle\DataFixtures\ORM\Catalyst;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Catalyst\UserBundle\Entity\User;

class LoadUserData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('admin')
            ->setPlainPassword('admin')
            ->setEmail('test@test.com')
            ->setName('Adminsitrator')
            ->setEnabled(true);
        
        $em->persist($userAdmin);
        $em->flush();
    }
    
    public function getOrder()
    {
        return 1;
    }
}