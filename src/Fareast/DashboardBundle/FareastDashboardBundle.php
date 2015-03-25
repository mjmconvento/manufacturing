<?php

namespace Fareast\DashboardBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FareastDashboardBundle extends Bundle
{
    public function getParent()
    {
        return 'CatalystDashboardBundle';
    }
}
