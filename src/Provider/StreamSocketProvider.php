<?php

namespace Jalle19\CertificateParser\Provider;

use Jalle19\CertificateParser\Exception\ConnectionTimeoutException;
use Jalle19\CertificateParser\Exception\DomainMismatchException;
use Jalle19\CertificateParser\Exception\NameResolutionException;
use Jalle19\CertificateParser\Exception\CertificateNotFoundException;
use Jalle19\CertificateParser\Exception\ConnectionFailedException;

/**
 * Class StreamSocketProvider
 * @package Jalle19\CertificateParser\Provider
 */
class StreamSocketProvider implements ProviderInterface
{

    const DEFAULT_TIMEOUT_SECONDS = 15;
    const DEFAULT_PORT            = 443;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var int
     */
    private $port;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var boolean
     */
    private $verifyPeerName;


    /**
     * StreamSocketProvider constructor.
     *
     * @param string  $hostname
     * @param int     $port           (optional)
     * @param int     $timeout        (optional)
     * @param boolean $verifyPeerName (optional)
     */
    public function __construct(
        $hostname,
        $port = self::DEFAULT_PORT,
        $timeout = self::DEFAULT_TIMEOUT_SECONDS,
        $verifyPeerName = true
    ) {
        $this->hostname       = $hostname;
        $this->port           = $port;
        $this->timeout        = $timeout;
        $this->verifyPeerName = $verifyPeerName;
    }


    /**
     * @inheritdoc
     */
    public function getRawCertificate()
    {
        $streamContext = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer'       => false,
                'verify_peer_name'  => $this->verifyPeerName,
            ],
        ]);

        try {
            $client = stream_socket_client(
                $this->getRequestUrl(),
                $errorNumber,
                $errorDescription,
                $this->timeout,
                STREAM_CLIENT_CONNECT,
                $streamContext
            );

            $response = stream_context_get_params($client);

            return $response['options']['ssl']['peer_certificate'];
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();

            // Check for name resolution errors
            if (strpos($errorMessage, 'getaddrinfo failed') !== false) {
                throw new NameResolutionException();
            }

            // Check for unknown SSL protocol (usually means no SSL is configured on the endpoint)
            if (strpos($errorMessage, 'GET_SERVER_HELLO:unknown protocol') !== false) {
                throw new CertificateNotFoundException();
            }

            // Check for domain mismatches
            if (strpos($errorMessage, 'did not match expected') !== false) {
                throw new DomainMismatchException();
            }

            // Check for connection timeouts
            if (strpos($errorMessage, 'timed out') !== false) {
                throw new ConnectionTimeoutException();
            }

            throw new ConnectionFailedException($errorMessage);
        }
    }


    /**
     * @return string
     */
    private function getRequestUrl()
    {
        return 'ssl://' . $this->hostname . ':' . $this->port;
    }

}
