<?php

namespace Catalyst\ContactBundle\Model\Controller;

trait HasPhones
{
    protected function updateHasPhones($o, $data, $is_new)
    {
        $em = $this->getDoctrine()->getManager();
        if(isset($data['phone_id'])){
            foreach ($data['phone_id'] as $phone_id)
            {
                $phone = $em->getRepository('CatalystContactBundle:Phone')->find($phone_id);
                // TODO: return error if address is not found or ignore
                $o->addPhone($phone);
            }
        }
    }
}

