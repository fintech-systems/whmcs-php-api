<?php

// WARNING! Ensure <?php is the very first line of this file

use WHMCS\Module\RegistrarSetting;

if (! defined('WHMCS')) {
    exit('This file cannot be accessed directly');
}

try {
    logModuleCall('Custom action: SetRegistrarSettingValue', 'Start', $_REQUEST, '', '', '');

    $setting = RegistrarSetting::where('registrar', '=', $_REQUEST['registrar'])
        ->where('setting', '=', $_REQUEST['setting'])
        ->first();

    $setting->value = $_REQUEST['value'];

    $setting->save();

    $apiresults = [
        'result' => 'success',
        'id' => $setting->id,
        'registrar' => $setting->registrar,
        'setting' => $setting->setting,
        'value' => $setting->value,
    ];
} catch (Exception $e) {
    $apiresults = [
        'result' => 'error',
        'message' => $e->getMessage(),
        'log' => $log,
    ];
}
