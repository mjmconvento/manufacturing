<?php

namespace Catalyst\CoreBundle\Template\Controller;

trait TrackUpdate
{
	protected function updateTrackUpdate($o, $data)
	{		
		$o->setUserUpdate($this->getUser());
	}
}