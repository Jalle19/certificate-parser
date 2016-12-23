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
     * ParserResult constructor.
     *
     * @param ParsedCertificate $parsedCertificate
     * @param array             $rawCertificate
     */
    public function __construct(ParsedCertificate $parsedCertificate, array $rawCertificate)
    {
        $this->parsedCertificate = $parsedCertificate;
        $this->rawCertificate    = $rawCertificate;
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

}
