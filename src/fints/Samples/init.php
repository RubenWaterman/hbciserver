<?php

/** @noinspection PhpUnhandledExceptionInspection */

/**
 * SAMPLE - Creates a new FinTs instance. This file mainly contains the configuration data for the phpFInTS library.
 */
require __DIR__ .'/../vendor/autoload.php';

// The configuration options up here are considered static wrt. the library's internal state and its requests.
// That is, even if you persist the FinTs instance, you need to be able to reproduce all this information from some
// application-specific storage (e.g. your database) in order to use the phpFinTS library.
$options = new \Fhp\Options\FinTsOptions();
$options->url = getenv('FINTEX_BANK_URL'); // HBCI / FinTS Url can be found here: https://www.hbci-zka.de/institute/institut_auswahl.htm (use the PIN/TAN URL)
$options->bankCode = getenv('FINTEX_BANK_CODE'); // Your bank code / Bankleitzahl
$options->productName = getenv('FINTEX_FINTS_REG'); // The number you receive after registration / FinTS-Registrierungsnummer
$options->productVersion = '1.0'; // Your own Software product version
$credentials = \Fhp\Options\Credentials::create(getenv('FINTEX_BANK_USERNAME'), getenv('FINTEX_BANK_PIN')); // This is NOT the PIN of your bank card!
$fints = \Fhp\FinTs::new($options, $credentials);
// $fints->setLogger(new \Tests\Fhp\CLILogger());

// Usage:
// $fints = require_once 'init.php';
return $fints;
