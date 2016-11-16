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
     * @expectedException \Jalle19\CertificateParser\Exception\NameResolutionException
     */
    public function testNonExistingDomain()
    {
        $provider = new StreamSocketProvider('example.does.not.exist');
        $provider->getRawCertificate();
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Exception\CertificateNotFoundException
     */
    public function testNoCertificateFound()
    {
        $provider = new StreamSocketProvider('connectivitycheck.gstatic.com', 80);
        $provider->getRawCertificate();
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Exception\DomainMismatchException
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
            StreamSocketProvider::DEFAULT_TIMEOUT_SECONDS, false);

        $this->assertTrue(is_resource($provider->getRawCertificate()));
    }


    /**
     * @expectedException \Jalle19\CertificateParser\Exception\ConnectionTimeoutException
     */
    public function testConnectionTimeoutException()
    {
        // Force a timeout to occur
        $provider = new StreamSocketProvider('example.com', 443, 0);
        $provider->getRawCertificate();
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
