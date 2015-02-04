<?php

namespace Catalyst\CoreBundle\Model\Controller;

trait TrackCreate
{
    protected function updateTrackCreate($o, $data, $is_new)
    {
        if ($is_new)
            $o->setUserCreate($this->getUser());
    }
}
