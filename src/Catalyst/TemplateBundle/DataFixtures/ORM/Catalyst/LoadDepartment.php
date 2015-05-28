<?php

namespace Catalyst\TemplateBundle\DataFixtures\ORM\Catalyst;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Catalyst\UserBundle\Entity\Department;
use DateTime;

class LoadDepartment  extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $user = $em->getRepository('CatalystUserBundle:User')->findOneByName('Administrator');
        $inv_acc = $em->getRepository('CatalystInventoryBundle:Account')->findOneByName('Department: Department 1');
        
        $department = new Department();
        $department->setName('Department 1')
            ->setDateCreate(new DateTime())
            ->setUserCreate($user)
            ->setInventoryAccount($inv_acc);

        $em->persist($department);
        $em->flush();

        $user->setDepartment($department);
        $em->persist($user);
        $em->flush();
    }
    
    public function getOrder()
    {
        return 4;
    }
}