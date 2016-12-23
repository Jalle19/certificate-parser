<?php

namespace Jalle19\CertificateParser\Tests;

use AcmePhp\Ssl\ParsedCertificate;
use Jalle19\CertificateParser\Parser;
use Jalle19\CertificateParser\ParserResults;
use Jalle19\CertificateParser\Provider\LocalFileProvider;

/**
 * Class ParserResultsTest
 * @package Jalle19\CertificateParser\Tests
 */
class ParserResultsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ParserResults
     */
    private $parserResults;


    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $parser   = new Parser();
        $provider = new LocalFileProvider(__DIR__ . '/../resources/ssl-cert-snakeoil.pem');

        $this->parserResults = $parser->parse($provider);
    }


    /**
     * Tests that getParsedCertificate() works correctly
     */
    public function testGetParsedCertificate()
    {
        $this->assertInstanceOf(ParsedCertificate::class, $this->parserResults->getParsedCertificate());
    }


    /**
     * Tests that getRawCertificate() works correctly
     */
    public function testGetRawCertificate()
    {
        $this->assertCount(1, $this->parserResults->getRawCertificate()['extensions']);
    }


    /**
     * Tests that getFingerprint() works correctly
     */
    public function testGetFingerprint()
    {
        $this->assertEquals('026e630d487a3921380f9d1e77c7163a62aa3f67', $this->parserResults->getFingerprint());
    }

}
