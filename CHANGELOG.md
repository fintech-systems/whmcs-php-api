# Changelog

All notable changes to `whmcs-api` will be documented in this file.

## v0.1.0 - 2022-10-23

- Updated all tests to use mocks
- Updated composer
- Fixed README to specify Pest is actually the test suite
- Fixed the current test suite to a test and a live system. It turns out WHMCS has an inconsistent way of saving telephone numbers, perhaps long ago no spaces but no it auto injects spaces for some countries. USA numbers also seems to selectively have dashes and sometimes not. This means some of the tests could be inconsistent in real-life and on other systems. Got 11 tests working but used data injected from the tests.

## v0.0.5 - 2021-10-27

- refactor to remove return statements in getclientbyphonenumber as return statement might lead to abnormal program termination via the include system

## v0.0.4 - 2021-10-27

- handling of space in telephone numbers

## v0.0.3 - 2021-10-24

- added ability to switch to another server (setServer(url, api identifier, api secret)
- added tests to test setServer ability
- updated .env.example with hypothetical server credentials environment variables

## v0.0.1 - 2021-10-23

- initial release
