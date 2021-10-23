<?php

namespace FintechSystems\Whmcs\Facades;

use Illuminate\Support\Facades\Facade;

class Whmcs extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'whmcs';
    }
}
