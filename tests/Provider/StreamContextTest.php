<?php

namespace Jalle19\CertificateParser\Tests\Provider;

use Jalle19\CertificateParser\Provider\StreamContext;

/**
 * Class StreamContextTest
 * @package Jalle19\CertificateParser\Tests\Provider
 */
class StreamContextTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param bool   $verifyPeerName
     * @param string $sniServerName
     * @param array  $expectedContext
     *
     * @dataProvider getArrayProvider
     */
    public function testGetArray($verifyPeerName, $sniServerName, $expectedContext)
    {
        $streamContext = new StreamContext($verifyPeerName, $sniServerName);

        $this->assertEquals($expectedContext, $streamContext->getArray());
    }


    /**
     * @return array
     */
    public function getArrayProvider()
    {
        return [
            [
                false,
                null,
                [
                    'ssl' => [
                        'capture_peer_cert' => true,
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                    ],
                ],
            ],
            [
                true,
                null,
                [
                    'ssl' => [
                        'capture_peer_cert' => true,
                        'verify_peer'       => false,
                        'verify_peer_name'  => true,
                    ],
                ],
            ],
            [
                true,
                null,
                [
                    'ssl' => [
                        'capture_peer_cert' => true,
                        'verify_peer'       => false,
                        'verify_peer_name'  => true,
                    ],
                ],
            ],
            [
                true,
                'example.com',
                [
                    'ssl' => [
                        'capture_peer_cert' => true,
                        'verify_peer'       => false,
                        'verify_peer_name'  => true,
                        'SNI_enabled'       => true,
                        'SNI_server_name'   => 'example.com',
                    ],
                ],
            ],
        ];
    }

}
