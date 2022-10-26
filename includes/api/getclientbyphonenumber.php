<?php

// Copy this file to your WHMCS includes/api folder, e.g.:
// scp -P22 getclientbyphonenumber.php user@server.example.com:/home/user/whmcs.example.com/public/includes/api/
// Add 'getclientbyphonenumber' to tblapi_roles
// By filling the variable called $apiresults one will have a return value
// Not sure if explicit return statements might lead to abnormal program termination and the WHMCS include type workflow 

use WHMCS\Database\Capsule;

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

try {
    // First check for the actual number
    $phoneNumber = $_REQUEST['phonenumber'];

    $client = Capsule::table('tblclients')
        ->where("phonenumber", $phoneNumber)
        ->first();

    if ($client) {

        $apiresults = [
            "result"   => "success",            
            'clientid' => $client->id,
        ];

    } else {
        // ...then check for the number but without spaces    
        $phoneNumberWithoutSpaces = str_replace(' ', '', $_REQUEST['phonenumber']);

        $client = Capsule::table('tblclients')->where("phonenumber", $phoneNumberWithoutSpaces)->first();

        if ($client) {
            $apiresults = [
                "result"   => "success",                
                'clientid' => $client->id,
            ];

        } else {

            $apiresults = [
                "result"  => "error",
                "message" => "a client with number $phoneNumber was not found",
            ];
        }
            
    }

    
} catch (Exception $e) {

    $apiresults = [
        "result" => "error",
        "message" => $e->getMessage(),
    ];
}
