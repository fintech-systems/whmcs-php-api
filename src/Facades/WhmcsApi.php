<?php

namespace FintechSystems\Whmcs\Facades;

use Illuminate\Support\Facades\Facade;

class WhmcsApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'whmcs';
    }
}
