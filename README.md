# WHMCS API
![GitHub release (latest by date)](https://img.shields.io/github/v/release/fintech-systems/whmcs-php-api) [![Build Status](https://app.travis-ci.com/fintech-systems/whmcs-php-api.svg?branch=main)](https://app.travis-ci.com/fintech-systems/whmcs-php-api) ![GitHub](https://img.shields.io/github/license/fintech-systems/whmcs-php-api)

A WHMCS API designed to run standalone or as part of a Laravel Application

Requirements:

- PHP 8.0
- WHMCS

# Why this package?

WHMCS already has an extensive API. Why build another API? The reason is quite simple:

The WHMCS API code examples relies on CURL. Mocking CURL is possible but complicated. Instead Laravel already has beautiful HTTP testing and Http::fake mocking. Using Laravel (and in this case, Pest) means application development is sped up tremendously. Essentially you get away from testing against development servers. Even if you're not using Laravel, the framework's testing ability means it's possibly to write more complex software that's fail safe.

# Usage

## WHMCS API Permissions

- You need to allow the IP address of the computer connecting to WHMCS
- You need to set API permissions

### Custom API calls

WHMCS removed the ability to add custom API calls but with a bit of hacking you can get it working again. 

An example of a custom API call would be to get a client by their phone number. Let's call this `getclientbyphonenumber`.

You'll first have to first code the API function and save it here:
`includes/api/getclientbyphoneumber.php`

```php
<?php

use WHMCS\Database\Capsule;

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

try {
    $client = Capsule::table('tblclients')
        ->where("phonenumber", $_REQUEST['phonenumber'])
        ->first();

    if ($client) {
        $apiresults = [
            "result" => "success",
            "message" => "ok",
            'clientid' => $client->id,
        ];
    } else {
        $apiresults = [
            "result" => "error",
            "message" => "not found",
        ];
    }
} catch (Exception $e) {
    $apiresults = ["result" => "error", "message" => $e->getMessage()];
}
```

Next to use the custom API call `getclientbyphonenumber` you need to manually update `tblapi_roles` to add it. Also remmeber to update it every time again you make a change because the UI will overwrite the custom API call.

```json
{"addclient":1,"getclientsdetails":1,"getclientbyphonenumber":1}
```

If you haven't added the PHP file yet, you'll get `API Function Not Found`

## Framework Agnostic PHP

```php
<?php

use FintechSystems\WhmcsApi\WhmcsApi;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server = [
    'url'            => $_ENV['WHMCS_URL'],
    'api_identifier' => $_ENV['WHMCS_API_IDENTIFIER'],
    'api_secret'     => $_ENV['WHMCS_API_SECRET'],
];

$api = new WhmcsApi($server);

$result = $api->getClients();
```

## Laravel Installation

Publish the configuration file:

`php artisan vendor:publish --tag=whmcs-config`

## Changelog

See [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

# Features

## Set Server

Provides the ability to connect to a secondary WHMCS server away from the Facade initiation

```php
Whmcs::setServer([
    'url'            => $_ENV['WHMCS_URL2'],
    'api_secret'     => $_ENV['WHMCS_API_SECRET2'],
    'api_identifier' => $_ENV['WHMCS_API_IDENTIFIER2'],
])
```

Change Package

## Change Package

Framework Agnostic PHP:

```php
$newServiceId = 5;

$api = new WhmcsApi;
$api->changePackage($newServiceId);
```

Laravel App:

## Change server

Since we're using a Facade that's instantiated when it's called, we need some other way to call the contructor when we're connecting to another server.

```php
public function setServer($server) {
        $this->url            = $server['url'];
        $this->api_identifier = $server['api_identifier'];
        $this->api_secret     = $server['api_secret'];
    }
```

```php
$newServiceId = 5;

WhmcsApi::changePackage($newServiceId);
```

Result:

A new package is applied to the service. If the package is linked to an API, the API will be called. 

# Testing

Run the following command to test:

./vendor/bin/pest

If you want to test individual tests, append this to the end of a test `->only();`.

## Invalid IP 127.0.0.1

If you get `Invalid IP 127.0.0.1` that means you haven't allowed WHMCS API access from localhost. 

Navigate here: https://whmcs.test/admin/configgeneral.php and make sure you add `127.0.0.1` to API IP Access Restriction.

## Invalid or missing credentials

If you get `Invalid or missing credentials` that means you haven't added API roles and API credentials. Both are required before you can test. Also be sure to add them to your `.env` file:

```
WHMCS_API_IDENTIFIER=
WHMCS_API_SECRET=
```

## Invalid Permissions: API action "addclient" is not allowed

If you get `Invalid Permissions: API action "addclient" is not allowed` that means although you've added API roles and credentials, your roles are not set up properly for the API call. Revisit roles and the requisite subsection and see where you have to click the checkbox to allow this API call.

## Storage folder examples

The `storage` folder has examples API responses, also used for caching during tests.

## Coverage reports

To regenerate coverage reports:

`XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html=tests/coverage-report`

See also `.travis.yml`

We have a badge for Coverage but it's problematic due to Github issues:<br>
![Codecov branch](https://img.shields.io/codecov/c/github/fintech-systems/whmcs-api/main) 

## Version Control

This application uses Semantic Versioning as per https://semver.org/

# Local Editing

For local editing, add this to `composer.json`:

```json
"repositories" : [
        {
            "type": "path",
            "url": "../whmcs-api"
        }
    ]
```

Then in `require` section:

```json
"fintech-systems/virtualmin-api": "dev-main",
```

# License

MIT

# Author

eugene (at) vander.host <br>
https://vander.host <br>
+27 82 309-6710
I'm available for WHMCS consulting.