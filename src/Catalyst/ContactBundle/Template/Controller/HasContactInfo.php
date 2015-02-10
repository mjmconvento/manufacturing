<?php

namespace Catalyst\ContactBundle\Template\Controller;

trait HasContactInfo
{
    use HasAddresses;
    use HasPhones;

    protected function updateContact($o, $data, $is_new)
    {
        $this->updateHasAddresses($o, $data, $is_new);
        $this->updateHasPhones($o, $data, $is_new);

        $o->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setMiddleName($data['middle_name'])
            ->setSalutation($data['salutation'])
            ->setEmail($data['email'])
            ->setContactTypeID($data['type_id']);
    }
}

