<?php

namespace Jalle19\CertificateParser\Provider;

use Jalle19\CertificateParser\Exception\DomainMismatchException;
use Jalle19\CertificateParser\Exception\InvalidUrlException;
use Jalle19\CertificateParser\Exception\NameResolutionException;
use Jalle19\CertificateParser\Exception\NoCertificateFoundException;
use Jalle19\CertificateParser\Exception\UnknownErrorException;

/**
 * Class StreamSocketProvider
 * @package Jalle19\CertificateParser\Provider
 */
class StreamSocketProvider implements ProviderInterface
{

    const DEFAULT_TIMEOUT_SECONDS = 15;
    const DEFAULT_PORT            = 443;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;


    /**
     * StreamSocketProvider constructor.
     *
     * @param string $url
     * @param int    $timeout (optional)
     */
    public function __construct($url, $timeout = self::DEFAULT_TIMEOUT_SECONDS)
    {
        $this->timeout = $timeout;
        $this->parseUrl($url);
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
                throw new NoCertificateFoundException();
            }

            // Check for domain mismatches
            if (strpos($errorMessage, 'did not match expected') !== false) {
                throw new DomainMismatchException();
            }

            throw new UnknownErrorException($errorMessage);
        }
    }


    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }


    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }


    /**
     * @param string $url
     *
     * @throws InvalidUrlException
     */
    private function parseUrl($url)
    {
        $parsedUrl = parse_url($url);

        if (array_key_exists('host', $parsedUrl)) {
            $this->host = $parsedUrl['host'];
        } elseif (array_key_exists('path', $parsedUrl) && !empty($parsedUrl['path'])) {
            $this->host = $parsedUrl['path'];
        } else {
            throw new InvalidUrlException('Unable to parse the URL "' . $url . '""');
        }

        if (array_key_exists('port', $parsedUrl)) {
            $this->port = $parsedUrl['port'];
        } else {
            $this->port = self::DEFAULT_PORT;
        }
    }


    /**
     * @return string
     */
    private function getRequestUrl()
    {
        return 'ssl://' . $this->getHost() . ':' . $this->getPort();
    }

}
