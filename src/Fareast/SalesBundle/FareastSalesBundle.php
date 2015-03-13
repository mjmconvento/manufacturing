<?php

namespace Fareast\SalesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FareastSalesBundle extends Bundle
{
    public function getParent()
    {
        return 'CatalystSalesBundle';
    }
}
