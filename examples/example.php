<?php

use AcmePhp\Ssl\Exception\CertificateParsingException;
use Jalle19\CertificateParser\Provider\Exception\ProviderException;
use Jalle19\CertificateParser\Provider\Exception\ConnectionTimeoutException;
use Jalle19\CertificateParser\Provider\Exception\DomainMismatchException;
use Jalle19\CertificateParser\Provider\Exception\NameResolutionException;
use Jalle19\CertificateParser\Provider\Exception\CertificateNotFoundException;
use Jalle19\CertificateParser\Provider\Exception\ConnectionFailedException;
use Jalle19\CertificateParser\Parser;
use Jalle19\CertificateParser\Provider\StreamSocketProvider;

require_once(__DIR__ . '/../vendor/autoload.php');

// Create a provider. The provider is used to retrieve the raw certificate details from a URL.
// If you don't want DomainMismatchException to be thrown if the peer name doesn't match, pass
// false as the last parameter to the constructor.
$provider = new StreamSocketProvider('www.google.com');

// Create the parser instance
$parser = new Parser();

// Parse the certificate and print some details about it. Handle all exception types separately
// to illustrate what can be thrown
try {
    $parserResult = $parser->parse($provider);

    // Now we can inspect the certificate
    $certificate = $parserResult->getParsedCertificate();

    echo 'Issuer:                  ' . $certificate->getIssuer() . PHP_EOL;
    echo 'Subject:                 ' . $certificate->getSubject() . PHP_EOL;
    echo 'Subject alternate names: ' . implode(', ', $certificate->getSubjectAlternativeNames()) . PHP_EOL;
    echo 'Valid until:             ' . $certificate->getValidTo()->format('r') . PHP_EOL;

    // We can also inspect the raw certificate directly
    $rawCertificate = $parserResult->getRawCertificate();
} catch (NameResolutionException $e) {

} catch (CertificateNotFoundException $e) {

} catch (DomainMismatchException $e) {

} catch (ConnectionTimeoutException $e) {

} catch (ConnectionFailedException $e) {
    // Catch-all exception for connection errors that haven't been specifically handled
    var_dump($e->getMessage());
} catch (ProviderException $e) {
    // All of the above exceptions inherit from this one, so if you don't what happened you
    // can just catch this
} catch (CertificateParsingException $e) {
    // The certificate was successfully retrieved but couldn't be parsed
    var_dump($e->getMessage());
}
