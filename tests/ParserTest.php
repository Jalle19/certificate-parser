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
     * @expectedException \Jalle19\CertificateParser\Exception\InvalidUrlException
     */
    public function testFailedParse()
    {
        new Parser(new StreamSocketProvider(false));
    }


    /**
     *
     */
    public function testSuccessfulParse()
    {
        $parser = new Parser(new StreamSocketProvider('www.google.com'));

        $this->assertInstanceOf(ParsedCertificate::class, $parser->getParsedCertificate());
    }

}
