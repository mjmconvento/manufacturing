<?php

namespace Catalyst\ContactBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\ContactBundle\Entity\Phone;
use Catalyst\ValidationException;

class PhoneController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'cnt_phone';
        $this->title = 'Phone';

        $this->list_title = 'Phone';
        $this->list_type = 'static';
    }

    protected function newBaseClass()
    {
        return new Phone();
    }

    protected function update($o, $data, $is_new = false)
    {
        $cnt = $this->get('catalyst_contact');
        $o->setPhoneType($cnt->getPhoneType($data['phone_type']))
            ->setNumber($data['number']);

        //set as primary number
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
            'is_primary' => $o->getIsPrimary(),
            'number' => $o->getNumber()
        );

        return $data;
    }
}
