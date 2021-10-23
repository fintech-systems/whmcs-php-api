<?php

namespace FintechSystems\WhmcsApi\Facades;

use Illuminate\Support\Facades\Facade;

class WhmcsApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'whmcs-api';
    }
}
