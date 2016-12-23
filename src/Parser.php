<?php

namespace Jalle19\CertificateParser;

use AcmePhp\Ssl\Certificate;
use AcmePhp\Ssl\Exception\CertificateParsingException;
use AcmePhp\Ssl\Parser\CertificateParser;
use Jalle19\CertificateParser\Provider\ProviderInterface;

/**
 * Class Parser
 * @package Jalle19\CertificateParser
 */
class Parser
{

    /**
     * Attempts to parse the certificate using the specified provider
     *
     * @param ProviderInterface $provider
     *
     * @throws CertificateParsingException
     * @throws ProviderException
     *
     * @return ParserResults
     */
    public function parse(ProviderInterface $provider)
    {
        $rawCertificate = $provider->getRawCertificate();

        // Convert the raw certificate to a PEM string and parse it
        openssl_x509_export($rawCertificate, $pemString);
        $parser            = new CertificateParser();
        $parsedCertificate = $parser->parse(new Certificate($pemString));

        return new ParserResults($parsedCertificate, openssl_x509_parse($rawCertificate),
            openssl_x509_fingerprint($pemString));
    }

}
