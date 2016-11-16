# certificate-parser

[![Build Status](https://travis-ci.org/Jalle19/certificate-parser.svg?branch=travis)](https://travis-ci.org/Jalle19/certificate-parser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Jalle19/certificate-parser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Jalle19/certificate-parser/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/Jalle19/certificate-parser/badge.svg)](https://coveralls.io/github/Jalle19/certificate-parser)

A proper SSL/TLS certificate parser library for PHP

## Motivation

There are a couple of other existing certificate parsers for PHP out there, but they're all lacking in some way. Some 
lack configurability (e.g. not being able to change the port to something other than 443), others have mediocre error 
handling (or none), while some don't allow you to parse certificates that are considered invalid (e.g. an expired or 
self-signed certificate).

## Features

* Completely configurable. This library uses *providers* to fetch the underlying X.509 certificate before parsing them. 
This means you can parse e.g. local PEM files too, not just certificates from remote URLs, as long as you write a 
provider for it.
* Fault-tolerant. Just because PHP's default settings trigger an error when parsing a certificate doesn't mean you 
don't want to parse it.
* Granular error handling. There are multiple exception types for various failure scenarios, so you can choose 
exactly how you want each type of error to be handled.

## Requirements

* PHP >= 7.0 with OpenSSL support

## Installation

```
composer require jalle19/certificate-parser
```

## Usage 

```php
<?php

use AcmePhp\Ssl\Exception\CertificateParsingException;
use Jalle19\CertificateParser\Exception\ConnectionTimeoutException;
use Jalle19\CertificateParser\Exception\DomainMismatchException;
use Jalle19\CertificateParser\Exception\NameResolutionException;
use Jalle19\CertificateParser\Exception\CertificateNotFoundException;
use Jalle19\CertificateParser\Exception\ConnectionFailedException;
use Jalle19\CertificateParser\Parser;
use Jalle19\CertificateParser\Provider\StreamSocketProvider;

require_once(__DIR__ . '/../vendor/autoload.php');

// Create a provider. The provider is used to retrieve the raw certificate details from a URL.
$provider = new StreamSocketProvider('www.google.com');

// Create the parser instance
$parser = new Parser($provider);

// Parse the certificate and print some details about it. Handle all exception types separately to illustrate what can 
// be thrown
try {
    $parser->parse();
} catch (NameResolutionException $e) {

} catch (CertificateNotFoundException $e) {

} catch (DomainMismatchException $e) {

} catch (ConnectionTimeoutException $e) {

} catch (ConnectionFailedException $e) {
    // Catch-all exception for connection errors that haven't been specifically handled
    var_dump($e->getMessage());
}

// Now we can inspect the certificate
$certificate = $parser->getParsedCertificate();

echo 'Issuer:                  ' . $certificate->getIssuer() . PHP_EOL;
echo 'Subject:                 ' . $certificate->getSubject() . PHP_EOL;
echo 'Subject alternate names: ' . implode(', ', $certificate->getSubjectAlternativeNames()) . PHP_EOL;
echo 'Valid until:             ' . $certificate->getValidTo()->format('r') . PHP_EOL;

// We can also inspect the raw certificate directly
$rawCertificate = $parser->getRawCertificate();
```

You can also find this example in the `examples/` directory. If you run it using `php examples/example.php` it should 
print something like this:

```
Issuer:                  Google Internet Authority G2
Subject:                 www.google.com
Subject alternate names: www.google.com
Valid until:             Thu, 26 Jan 2017 01:13:00 +0000
```

### Writing a custom provider

This library ships with only a single provider, `StreamSocketProvider`. If it doesn't suit your needs, create a new 
provider by implementing the `ProviderInterface` interface.

## License

MIT

## Credits

* https://github.com/acmephp/ssl for the actual parser
* https://github.com/spatie/ssl-certificate and others for inspiration
