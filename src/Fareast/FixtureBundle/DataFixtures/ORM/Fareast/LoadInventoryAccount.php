<?php

namespace Fareast\FixtureBundle\DataFixtures\ORM\Catalyst;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Catalyst\InventoryBundle\Entity\Account;
use DateTime;

class InventoryAccount  extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $user = $em->getRepository('CatalystUserBundle:User')->findOneByName('Administrator');
        $name = ['Department: Department 1', 'Warehouse: Main Warehouse', 'Warehouse: Adjustment Warehouse', 'Warehouse: Production Tank'];

        foreach($name as $n)
        {
            $account = new Account();
            $account->setName($n)
                ->setDateCreate(new DateTime())
                ->setUserCreate($user);

            $em->persist($account);
        }

        $em->flush();

    }
    
    public function getOrder()
    {
        return 3;
    }
}