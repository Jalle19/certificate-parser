<?php

namespace Jalle19\CertificateParser\Tests;

use AcmePhp\Ssl\ParsedCertificate;
use Jalle19\CertificateParser\Parser;
use Jalle19\CertificateParser\Provider\StreamSocketProvider;

/**
 * Class ParserTest
 * @package Jalle19\CertificateParser\Tests
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Jalle19\CertificateParser\Exception\NameResolutionException
     */
    public function testFailedParse()
    {
        $parser = new Parser(new StreamSocketProvider('non.existing.domain'));
        $parser->parse();
    }


    /**
     *
     */
    public function testSuccessfulParse()
    {
        $parser = new Parser(new StreamSocketProvider('www.google.com'));
        $parser->parse();

        $this->assertInstanceOf(ParsedCertificate::class, $parser->getParsedCertificate());
    }

}
