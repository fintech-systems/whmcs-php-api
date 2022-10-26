<?php // WARNING! Ensure <?php is the very first line of this file

use WHMCS\Database\Capsule;
use \WHMCS\Module\RegistrarSetting;

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

try {        
    logModuleCall("Custom action: SetRegistrarValue", "Init", $_REQUEST, "", "", "");

    $registrar = $_REQUEST['registrar'];
    $value = $_REQUEST['value'];

    $setting = RegistrarSetting::where('registrar', '=', $registrar)->first();
    $setting->value = $value;
    $setting->save();        
    
    $apiresults = [
        "result" => "success",        
        "id" => $setting->id,
        "registrar" => $setting->registrar,
        "setting" => $setting->setting,
        "value" => $setting->value,
    ];    
} catch (Exception $e) {
    $apiresults = [
        "result" => "error",
        "message" => $e->getMessage(),
        "log" => $log,
    ];
}
