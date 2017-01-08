<?php

namespace Jalle19\CertificateParser\Tests\Provider;

use Jalle19\CertificateParser\Provider\StreamContext;
use Jalle19\CertificateParser\Provider\StreamSocketProvider;

/**
 * Class StreamSocketProviderTest
 * @package Jalle19\CertificateParser\Tests\Provider
 */
class StreamSocketProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param string $url
     *
     * @dataProvider properCertificateProvider
     */
    public function testProperCertificateHandling($url)
    {
        $provider = new StreamSocketProvider($url);
        $this->assertTrue(is_resource($provider->getRawCertificate()));
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Provider\Exception\NameResolutionException
     */
    public function testNonExistingDomain()
    {
        $provider = new StreamSocketProvider('example.does.not.exist');
        $provider->getRawCertificate();
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Provider\Exception\CertificateNotFoundException
     */
    public function testNoCertificateFound()
    {
        $provider = new StreamSocketProvider('connectivitycheck.gstatic.com', 80);
        $provider->getRawCertificate();
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Provider\Exception\DomainMismatchException
     */
    public function testDomainMismatch()
    {
        $provider = new StreamSocketProvider('wrong.host.badssl.com');
        $provider->getRawCertificate();
    }


    /**
     *
     */
    public function testVerifyPeerName()
    {
        $provider = new StreamSocketProvider('wrong.host.badssl.com', StreamSocketProvider::DEFAULT_PORT,
            StreamSocketProvider::DEFAULT_TIMEOUT_SECONDS, new StreamContext(false));

        $this->assertTrue(is_resource($provider->getRawCertificate()));
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Provider\Exception\ConnectionTimeoutException
     */
    public function testConnectionTimeoutException()
    {
        // Force a timeout to occur
        $provider = new StreamSocketProvider('example.com', 443, 0);
        $provider->getRawCertificate();
    }


    /**
     * Tests that the stream context is stored and handled properly
     */
    public function testStreamContext()
    {
        $provider = new StreamSocketProvider('example.com');
        $this->assertNotNull($provider->getStreamContext());

        $streamContext = new StreamContext(false, 'example.com');
        $provider->setStreamContext($streamContext);
        $this->assertEquals($streamContext, $provider->getStreamContext());
    }


    /**
     * @return array
     */
    public function properCertificateProvider()
    {
        return [
            // Completely valid
            ['www.google.com'],
            // Expired
            ['expired.badssl.com'],
            // Self-signed
            ['self-signed.badssl.com'],
            // Untrusted root
            ['untrusted-root.badssl.com'],
            // Revoked
            ['revoked.badssl.com'],
            // Incomplete chain
            ['incomplete-chain.badssl.com'],
        ];
    }

}
