<?php

use FintechSystems\Whmcs\Whmcs;
use Illuminate\Support\Facades\Http;
use FintechSystems\Whmcs\Tests\Config;
use FintechSystems\Whmcs\Facades\Whmcs as WhmcsFacade;

$config = new Config();

test('php pest is installed and operational', function () {
    expect(true)->toBeTrue();
});

// This will ensure you have a development server installed locally and is skipped by default.
test('it can access a test whmcs installation', function () {
    // If using Laravel Valet, https://whmcs.test won't have a legitimate certificate so let's skip checking for that.
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    file_get_contents(env('WHMCS_URL'), false, stream_context_create($arrContextOptions));

    $this->assertEquals($http_response_header[0], "HTTP/1.1 200 OK");
})->skip();

test("adding a new user to WHMCS doesn't return an invalid IP error", function () use ($config) {
    $whmcs = new Whmcs($config->server);

    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "success",
            "clientid" => 1,
            "owner_id" => 1,
        ])
    ]);

    $result = $whmcs->addUser([
        'password2'   => "password",
        'firstname'   => 'User1',
        'lastname'    => 'Lastname1',
        'email'       => 'user113331@example.com',
        'address1'    => '123 Penny Lane',
        'city'        => 'Beverly Hills',
        'state'       => 'California',
        'postcode'    => '90210',
        'country'     => 'US',
        'phonenumber' => '+27.66 245 4302',
    ]);

    expect($result)->toHaveKey('result', 'success');
    expect($result)->toHaveKey('clientid');
    expect($result)->toHaveKey('owner_id');
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
            "result" => "success",
            "clientid" => 1,
            "owner_id" => 1,
        ])
    ]);

    $result = $whmcs->addUser([
        'email'       => 'user23232@example.co.za',
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

    expect($result)->toHaveKey('result', 'success');
    expect($result)->toHaveKey('clientid');
    expect($result)->toHaveKey('owner_id');
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

test("it can retrieve at least two domains", function () {
    Http::fake([
        'https://whmcs.test/includes/api.php' => Http::response([
            "result" => "success",
            "numreturned" => 2,
            "domains" => [
                "domain" => [
                    0 => [
                        "id" => 1,
                        "userid" => 1,
                        "orderid" => 1,
                        "regtype" => "Register",
                        "domainname" => "1234.co.za",
                        "registrar" => "email",
                    ],
                    1 => [
                        "id" => 2,
                        "userid" => 1,
                        "orderid" => 2,
                        "regtype" => "Transfer",
                        "domainname" => "example.co.za",
                        "registrar" => "email",
                    ],
                ]
            ]
        ])
    ]);

    $result = WhmcsFacade::getClientsDomains();

    expect($result)->toHaveKey('result', 'success');
    expect($result)->toHaveKey('numreturned', 2);
    expect($result)->toHaveKey('domains');

    $count = count($result['domains']['domain']);

    expect($count)->toBe(2);
});
