<?php

namespace Catalyst\ContactBundle\Template\Controller;

trait HasContactInfo
{
    use HasAddresses;
    use HasPhones;

    protected function updateContact($o, $data, $is_new)
    {
        $cnt = $this->get('catalyst_contact');
        $this->updateHasAddresses($o, $data, $is_new);
        $this->updateHasPhones($o, $data, $is_new);
        $type = $cnt->getContactType($data['contact_type']);
        if('individual' === strtolower($type->getName()))
        {
            $o->setFirstName($data['first_name']);
        }else {
            $o->setFirstName($data['company_name']);
        }
            $o ->setEmail($data['email'])
                ->setLastName($data['last_name'])
                ->setMiddleName($data['middle_name'])
                ->setSalutation($data['salutation'])
                ->setContactType($type);
    }
    
    protected function padFormContactInfo(&$params){
        $cnt = $this->get('catalyst_contact');
        $this->padFormPhoneType($params);
        
        $params['contact_opts'] = $cnt->getContactTypeOptions();
    }
}

