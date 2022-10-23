<?php

use FintechSystems\Whmcs\Whmcs;
use Illuminate\Support\Facades\Http;
use FintechSystems\Whmcs\Tests\Config;
use FintechSystems\Whmcs\Facades\Whmcs as WhmcsFacade;

$config = new Config();

test('it can test', function () {
    expect(true)->toBeTrue();
});

test('it can access the billing system test installation', function () {
    // This will ensure you have a development server installed locally
    // https://whmcs.test won't have a legitimate certificate so let's skip checking for that.
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    
    file_get_contents(env('WHMCS_URL'), false, stream_context_create($arrContextOptions));

    $this->assertEquals($http_response_header[0], "HTTP/1.1 200 OK");
});

test("the billing system doesn't return an invalid IP error", function () use ($config) {
    $whmcs = new Whmcs($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "error",
            "message" => "A user already exists with that email address"
        ])
    ]);

    $result = $whmcs->addUser([
        'password2'   => "password",
        'firstname'   => 'User1',
        'lastname'    => 'Lastname1',
        'email'       => 'user1@example.com',
        'address1'    => '123 Penny Lane',
        'city'        => 'Beverly Hills',
        'state'       => 'California',
        'postcode'    => '90210',
        'country'     => 'US',
        'phonenumber' => '+27.66 245 4302',
    ]);

    expect($result)->toHaveKey('result', 'error');

    expect($result)->toHaveKey('message', 'A user already exists with that email address');
});

test("it will add an USA user with telephone number and dashes to the system for later testing", function () use ($config) {
    $whmcs = new Whmcs($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "success",
            "clientid" => 1,
            "owner_id" => 1,
        ])
    ]);
   
    $result = $whmcs->addUser([
        'email'       => 'user1@example.com',
        'password2'   => "password",
        'firstname'   => 'First Name',
        'lastname'    => 'Last Name',        
        'address1'    => '123 Penny Lane',
        'city'        => 'Beverly Hills',
        'state'       => 'California',
        'postcode'    => '90210',
        'country'     => 'US',
        'phonenumber' => '+1.408-555-1234',
    ]);
    
    expect($result)->toHaveKey('result', 'success');
    expect($result)->toHaveKey('clientid');
    expect($result)->toHaveKey('owner_id');

});

test('it can add a user to the billing system', function () use ($config) {
    $whmcs = new Whmcs($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "error",
            "message" => "A user already exists with that email address"
        ])
    ]);

    $result = $whmcs->addUser([
        'email'       => 'user2@example.co.za',
        'password2'   => "password",
        'firstname'   => 'First Name',
        'lastname'    => 'Last Name',        
        'address1'    => '1 Kloof Street',
        'city'        => 'Cape Town',
        'state'       => 'Western Cape',
        'postcode'    => '8001',
        'country'     => 'ZA',
        'phonenumber' => '+27.662454302',
    ]);

    expect($result)->toHaveKey('result', 'error');

    expect($result)->toHaveKey('message', 'A user already exists with that email address');
});

test('it can find a South African user by telephone number in the billing system', function () use ($config) {
    $whmcs = new Whmcs($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "success",
            "message" => "ok",
            "clientid" => 2,
        ])
    ]);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => "+27.82 309 6710",
    ]);

    expect($result)->toHaveKey('result', 'success');
    expect($result)->toHaveKey('message', 'ok');
    expect($result)->toHaveKey('clientid');
});

test('it can find a South African user by telephone number without spaces in the billing system', function () use ($config) {
    $whmcs = new Whmcs($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "success",
            "message" => "ok",
            "clientid" => 3,
        ])
    ]);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => "+27.662454302",
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test('it can find a South African user by telephone with spaces in the billing system', function () use ($config) {
    $whmcs = new Whmcs($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "success",
            "message" => "ok",
            "clientid" => 4,
        ])
    ]);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => "+27.66 245 4302",
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test('it can find a USA user by telephone number in the billing system', function () use ($config) {
    $whmcs = new Whmcs($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "success",
            "message" => "ok",
            "clientid" => 5,
        ])
    ]);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => "+1.408-555-1234",
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test('it can connect to a WHMCS instance using the Laravel facade and pull a clients details', function () {
    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "success",
            "user" => 1,
            "clientid" => 1,
        ])
    ]);

    $result = WhmcsFacade::getClientsDetails([
        'clientid' => 1
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test("it can connect to a secondary WHMCS instance using a Laravel facade and retrieve a client's details", function () {
    WhmcsFacade::setServer(
        [
            'url'            => env('WHMCS_URL2'),
            'api_secret'     => env('WHMCS_API_SECRET2'),
            'api_identifier' => env('WHMCS_API_IDENTIFIER2'),
        ]
    );

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "success",
            "user" => 1,
            "clientid" => 1,
        ])
    ]);

    $result = WhmcsFacade::getClientsDetails([
        'clientid' => 1
    ]);

    expect($result)->toHaveKey('result', 'success');
});
