<?php

namespace Fareast\PurchasingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FareastPurchasingBundle extends Bundle
{
	public function getParent()
    {
        return 'CatalystPurchasingBundle';
    }
}
