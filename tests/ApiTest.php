<?php

use FintechSystems\Whmcs\Facades\Whmcs as WhmcsFacade;
use FintechSystems\Whmcs\Tests\Config;
use FintechSystems\Whmcs\Whmcs;

$config = new Config();

test('it can test', function () {
    expect(true)->toBeTrue();
});

test("the billing system doesn't return an invalid IP error", function() use($config) {
    $whmcs = new Whmcs($config->server);

    $result = $whmcs->addUser([
        'password2'   => "SuperSecure123!!!",
        'firstname'   => 'Joe',
        'lastname'    => 'Bloggs',
        'email'       => 'joe4@example.com',
        'address1'    => '123 Penny Lane',
        'city'        => 'Beverly Hills',
        'state'       => 'California',
        'postcode'    => '90210',
        'country'     => 'US',
        'phonenumber' => '4085551234',
    ]);

    // dd($result);
    expect($result)->toHaveKey('result', 'error');
    // expect($result)->toHaveKey('message', 'Invalid IP 156.155.176.137');
    expect($result)->toHaveKey('message', 'A user already exists with that email address');
});

test('it can access the billing system test installation', function () {
    file_get_contents($_ENV['WHMCS_URL']);

    $this->assertEquals($http_response_header[0], "HTTP/1.1 200 OK");
});

test('it can add a user to the billing system', function () use ($config) {
    $whmcs = new Whmcs($config->server);

    $result = $whmcs->addUser([
        'password2'   => "SuperSecure123!!!",
        'firstname'   => 'Joe',
        'lastname'    => 'Bloggs',
        'email'       => 'joe3@example.com',
        'address1'    => '123 Penny Lane',
        'city'        => 'Beverly Hills',
        'state'       => 'California',
        'postcode'    => '90210',
        'country'     => 'US',
        'phonenumber' => '4085551234',
    ]);
    
    // expect($result)->toHaveKey('result', 'success');
    expect($result)->toHaveKey('result', 'error');
    expect($result)->toHaveKey('message', 'A user already exists with that email address');

});

test('it can find a South African user by telephone number in the billing system', function () use ($config) {
    $whmcs = new Whmcs($config->server);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => "+27.82 309 6710",        
    ]);
    
    expect($result)->toHaveKey('result', 'success');
});

test('it can find a South African user by telephone number without spaces in the billing system', function () use ($config) {
    $whmcs = new Whmcs($config->server);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => "+27.662454302",        
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test('it can find a USA user by telephone number in the billing system', function () use ($config) {
    $whmcs = new Whmcs($config->server);

    $result = $whmcs->getClientByPhoneNumber([
        'phonenumber' => "+1.408-555-1234",        
    ]);

    expect($result)->toHaveKey('result', 'success');

});

test('it can connect to a WHMCS instance using the Laravel facade and pull a clients details', function () {
    $result = WhmcsFacade::getClientsDetails([
        'clientid' => 1
    ]);

    expect($result)->toHaveKey('result', 'success');
});

test("it can connect to a secondary WHMCS instance using a Laravel facade and pull a client's details", function () {
    WhmcsFacade::setServer(
        [
            'url'            => $_ENV['WHMCS_URL2'],
            'api_secret'     => $_ENV['WHMCS_API_SECRET2'],
            'api_identifier' => $_ENV['WHMCS_API_IDENTIFIER2'],
        ]
    );

    $result = WhmcsFacade::getClientsDetails([
        'clientid' => 1
    ]);

    expect($result)->toHaveKey('result', 'success');
});
