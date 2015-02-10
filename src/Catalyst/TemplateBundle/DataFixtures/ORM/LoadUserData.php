<?php

namespace Catalyst\TemplateBundle\DataFixtures\ORM;

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
        $userAdmin->setUsername('admin');
        $userAdmin->setPlainPassword('admin');
        $userAdmin->setEmail('test@test.com');
        $userAdmin->setEnabled(true);
        $em->persist($userAdmin);
        $em->flush();
    }
}