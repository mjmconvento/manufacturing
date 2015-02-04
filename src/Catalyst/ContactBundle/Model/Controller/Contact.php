<?php

namespace Catalyst\ContactBundle\Model\Controller;

trait Contact
{
    use HasAddresses;

    protected function updateContact($o, $data, $is_new)
    {
        $this->updateHasAddresses($o, $data, $is_new);

        $o->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setMiddleName($data['middle_name'])
            ->setSalutation($data['salutation'])
            ->setEmail($data['email']);
    }
}

