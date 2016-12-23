<?php

namespace Jalle19\CertificateParser;

use AcmePhp\Ssl\ParsedCertificate;

/**
 * Class ParserResults
 * @package Jalle19\CertificateParser
 */
class ParserResults
{

    /**
     * @var ParsedCertificate
     */
    private $parsedCertificate;

    /**
     * @var array
     */
    private $rawCertificate;

    /**
     * @var string the certificate fingerprint
     */
    private $fingerprint;


    /**
     * ParserResult constructor.
     *
     * @param ParsedCertificate $parsedCertificate
     * @param array             $rawCertificate
     * @param string            $fingerprint
     */
    public function __construct(ParsedCertificate $parsedCertificate, array $rawCertificate, $fingerprint)
    {
        $this->parsedCertificate = $parsedCertificate;
        $this->rawCertificate    = $rawCertificate;
        $this->fingerprint       = $fingerprint;
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
        return $this->rawCertificate;
    }


    /**
     * @return string
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

}
