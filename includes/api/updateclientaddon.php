<?php // WARNING! Ensure <?php is the very first line of this file

use WHMCS\Database\Capsule;

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

try {
    logModuleCall("Add custom API actions", "Start", $_REQUEST, "", "", "");

    $apiActions = $_REQUEST['apis'];

    $log .= "Requested new API actions: " . implode('|', $apiActions) . "\n";

    $apiRoles = Capsule::table('tblapi_roles')
        ->first();

    $currentPermissions = $apiRoles->permissions;

    // Remove the last brace...
    $updatedPermissions = str_replace("}", "", $currentPermissions);

    // ...then check and remove the custom API action
    foreach ($apiActions as $apiAction) {
        $updatedPermissions = str_replace(",\"$apiAction\":1", "", $updatedPermissions);
        // now add it again...
        $updatedPermissions .= ",\"$apiAction\":1";
    }
    $updatedPermissions .= "}";
        
    $apiRoles = Capsule::table('tblapi_roles');
    $apiRoles->update(
        [
            'permissions' => $updatedPermissions
        ]
    );

    $apiresults = [
        "result"   => "success",
        "log" => $log,
        "permissions" => $updatedPermissions,        
    ];
} catch (Exception $e) {
    $apiresults = [
        "result" => "error",
        "message" => $e->getMessage(),
        "log" => $log,
    ];
}
