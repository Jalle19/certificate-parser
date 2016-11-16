<?php

namespace Jalle19\CertificateParser;

use AcmePhp\Ssl\Certificate;
use AcmePhp\Ssl\Exception\CertificateParsingException;
use AcmePhp\Ssl\ParsedCertificate;
use AcmePhp\Ssl\Parser\CertificateParser;
use Jalle19\CertificateParser\Provider\ProviderInterface;

/**
 * Class Parser
 * @package Jalle19\CertificateParser
 */
class Parser
{

    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @var array
     */
    private $rawCertificate;

    /**
     * @var ParsedCertificate
     */
    private $parsedCertificate;


    /**
     * Parser constructor.
     *
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }


    /**
     * Attempts to parse the certificate
     *
     * @throws CertificateParsingException
     */
    public function parse()
    {
        $this->rawCertificate = $this->provider->getRawCertificate();

        openssl_x509_export($this->rawCertificate, $pemString);
        $parser         = new CertificateParser();
        $rawCertificate = new Certificate($pemString);

        $this->parsedCertificate = $parser->parse($rawCertificate);
    }


    /**
     * @return ParsedCertificate
     */
    public function getParsedCertificate()
    {
        return $this->parsedCertificate;
    }


    /**
     * @return array
     */
    public function getRawCertificate()
    {
        return openssl_x509_parse($this->rawCertificate);
    }

}
