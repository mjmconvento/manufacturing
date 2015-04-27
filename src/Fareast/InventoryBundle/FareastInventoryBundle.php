<?php

namespace Fareast\InventoryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FareastInventoryBundle extends Bundle
{
    public function getParent()
    {
        return 'CatalystInventoryBundle';
    }
}
