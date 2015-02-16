<?php

namespace Catalyst\ContactBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\ContactBundle\Entity\Address;
use Catalyst\ValidationException;

class AddressController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'cnt_address';
        $this->title = 'Address';

        $this->list_title = 'Addresses';
        $this->list_type = 'static';
    }

    protected function newBaseClass()
    {
        return new Address();
    }

    protected function update($o, $data, $is_new = false)
    {
        $o->setName($data['name'])
            ->setStreet($data['street'])
            ->setCity($data['city'])
            ->setState($data['state'])
            ->setCountry($data['country'])
            ->setLatitude($data['latitude'])
            ->setLongitude($data['longitude']);

        //set as primary address
        if(isset($data['is_primary']) && $data['is_primary'] == 1)
            $o->setIsPrimary();

        $o->setUserCreate($this->getUser());
    }

    protected function getObjectLabel($object)
    {
        return $object->getName();
    }

    protected function buildData($o)
    {
        $data = array(
            'id' => $o->getID(),
            'name' => $o->getName(),
            'address' => $o->getAddress(),
            'contact_number' => $o->getContactNumber(),
            'email' => $o->getEmail(),
            'contact_person' => $o->getContactPerson(),
            'notes' => $o->getNotes(),
            'is_primary' => $o->getIsPrimary(),
        );

        return $data;
    }
}
