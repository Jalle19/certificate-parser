<?php

namespace Jalle19\CertificateParser\Tests\Provider;

use Jalle19\CertificateParser\Provider\StreamSocketProvider;

/**
 * Class StreamSocketProviderTest
 * @package Jalle19\CertificateParser\Tests\Provider
 */
class StreamSocketProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Jalle19\CertificateParser\Exception\InvalidUrlException
     */
    public function testInvalidUrlException()
    {
        new StreamSocketProvider(null);
    }


    /**
     * Tests that proper URLs are parsed correctly
     *
     * @param string $url
     * @param string $expectedHost
     * @param int    $expectedPort
     *
     * @dataProvider properUrlProvider
     */
    public function testProperUrlHandling($url, $expectedHost, $expectedPort)
    {
        $provider = new StreamSocketProvider($url);

        $this->assertEquals($expectedHost, $provider->getHost());
        $this->assertEquals($expectedPort, $provider->getPort());
    }


    /**
     * @param string $url
     *
     * @dataProvider properCertificateProvider
     */
    public function testProperCertificateHandling($url)
    {
        $provider       = new StreamSocketProvider($url);
        $rawCertificate = $provider->getRawCertificate();

        $this->assertTrue(is_resource($rawCertificate));
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Exception\NameResolutionException
     */
    public function testNonExistingDomain()
    {
        $provider = new StreamSocketProvider('https://example.does.not.exist');
        $provider->getRawCertificate();
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Exception\NoCertificateFoundException
     */
    public function testNoCertificateFound()
    {
        $provider = new StreamSocketProvider('http://connectivitycheck.gstatic.com:80');
        $provider->getRawCertificate();
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Exception\DomainMismatchException
     */
    public function testDomainMismatch()
    {
        $provider = new StreamSocketProvider('https://wrong.host.badssl.com/');
        $provider->getRawCertificate();
    }


    /**
     * @return array
     */
    public function properUrlProvider()
    {
        return [
            ['example.com', 'example.com', 443],
            ['example.com:4430', 'example.com', 4430],
            ['https://example.com', 'example.com', 443],
            ['tcp://example.com', 'example.com', 443],
            ['ssl://example.com', 'example.com', 443],
        ];
    }


    /**
     * @return array
     */
    public function properCertificateProvider()
    {
        return [
            // Completely valid
            ['https://www.google.com'],
            // Expired
            ['https://expired.badssl.com'],
            // Self-signed
            ['https://self-signed.badssl.com/'],
            // Untrusted root
            ['https://untrusted-root.badssl.com/'],
            // Revoked
            ['https://revoked.badssl.com/'],
            // Incomplete chain
            ['https://incomplete-chain.badssl.com/'],
        ];
    }

}
