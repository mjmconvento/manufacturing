<?php

namespace Serenitea\InventoryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SereniteaInventoryBundle extends Bundle
{
	public function getParent()
	{
		return 'CatalystInventoryBundle';
	}
}
