# WHMCS API
![GitHub release (latest by date)](https://img.shields.io/github/v/release/fintech-systems/packagist-boilerplate) [![Build Status](https://app.travis-ci.com/fintech-systems/packagist-boilerplate.svg?branch=main)](https://app.travis-ci.com/fintech-systems/packagist-boilerplate) ![GitHub](https://img.shields.io/github/license/fintech-systems/packagist-boilerplate)

A WHMCS API designed to run standalone or as part of a Laravel Application

Requirements:

- PHP 8.0
- WHMCS

# Usage

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

`php artisan vendor:publish`

# Features

Change Package

## Change Package

Framework Agnostic PHP:

```php
$newServiceId = 5;

$api = new WhmcsApi;
$api->changePackage($newServiceId);
```

Laravel App:


```php
$newServiceId = 5;

WhmcsApi::changePackage($newServiceId);
```

Result:

A new package is applied to the service. If the package is linked to an API, the API will be called. 

# Testing

We love testing! Use the command below to run the tests.

`vendor/bin/phpunit --exclude-group=live`

The exclude is so as to avoid Live API calls which may cause tests to fail

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

