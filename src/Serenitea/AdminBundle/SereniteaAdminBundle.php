<?php

namespace Serenitea\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SereniteaAdminBundle extends Bundle
{
	public function getParent()
	{
		return 'CatalystUserBundle';
	}
}
