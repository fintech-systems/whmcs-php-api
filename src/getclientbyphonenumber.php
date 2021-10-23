<?php

// Copy this file to your WHMCS includes/api folder, e.g.:
// scp -P22 getclientbyphonenumber.php user@server.example.com:/home/user/whmcs.example.com/public/includes/api/
// Add 'getclientbyphonenumber' to tblapi_roles

use WHMCS\Database\Capsule;

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

try {
    $client = Capsule::table('tblclients')->where("phonenumber",   $_REQUEST['phonenumber'])->first();

    if ($client) {
        $apiresults = [
            "result" => "success",
            "message" => "ok",
            'clientid' => $client->id
        ];
    } else {
        $apiresults = [
            "result" => "error",
            "message" => "not found"
        ];
    }
} catch (Exception $e) {
    $apiresults = ["result" => "error", "message" => $e->getMessage()];
}
