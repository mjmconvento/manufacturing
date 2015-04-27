<?php

namespace Fareast\ManufacturingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FareastManufacturingBundle extends Bundle
{
    public function getParent()
    {
        return 'CatalystManufacturingBundle';
    }
}
