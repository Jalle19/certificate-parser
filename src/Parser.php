<?php

namespace Jalle19\CertificateParser;

use AcmePhp\Ssl\Certificate;
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
     */
    public function parse()
    {
        $this->rawCertificate = $this->provider->getRawCertificate();
    }


    /**
     * @return ParsedCertificate
     */
    public function getParsedCertificate()
    {
        openssl_x509_export($this->rawCertificate, $pemString);
        $parser         = new CertificateParser();
        $rawCertificate = new Certificate($pemString);

        return $parser->parse($rawCertificate);
    }


    /**
     * @return array
     */
    public function getRawCertificate()
    {
        return openssl_x509_parse($this->rawCertificate);
    }

}
