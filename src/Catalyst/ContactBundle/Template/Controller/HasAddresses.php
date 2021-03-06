<?php

namespace Catalyst\ContactBundle\Template\Controller;

trait HasAddresses
{
    protected function updateHasAddresses($o, $data, $is_new)
    {
        $em = $this->getDoctrine()->getManager();
        if(isset($data['address_id'])){
            foreach ($data['address_id'] as $add_id)
            {
                $address = $em->getRepository('CatalystContactBundle:Address')->find($add_id);
                // TODO: return error if address is not found or ignore
                $o->addAddress($address);
            }
        }
    }
}

