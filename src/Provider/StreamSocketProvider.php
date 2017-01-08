<?php

namespace Jalle19\CertificateParser\Provider;

use Jalle19\CertificateParser\Provider\Exception\ConnectionTimeoutException;
use Jalle19\CertificateParser\Provider\Exception\DomainMismatchException;
use Jalle19\CertificateParser\Provider\Exception\NameResolutionException;
use Jalle19\CertificateParser\Provider\Exception\CertificateNotFoundException;
use Jalle19\CertificateParser\Provider\Exception\ProviderException;

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
     * @var StreamContext
     */
    private $streamContext;

    /**
     * @var array maps partial error messages to exception types
     */
    private static $errorExceptionMap = [
        // Check for name resolution errors
        'getaddrinfo failed'                => NameResolutionException::class,
        // Check for unknown SSL protocol (usually means no SSL is configured on the endpoint)
        'GET_SERVER_HELLO:unknown protocol' => CertificateNotFoundException::class,
        // Check for domain mismatches
        'did not match expected'            => DomainMismatchException::class,
        // Check for connection timeouts
        'timed out'                         => ConnectionTimeoutException::class,
    ];


    /**
     * StreamSocketProvider constructor.
     *
     * @param string             $hostname
     * @param int                $port          (optional)
     * @param int                $timeout       (optional)
     * @param StreamContext|null $streamContext (optional)
     */
    public function __construct(
        $hostname,
        $port = self::DEFAULT_PORT,
        $timeout = self::DEFAULT_TIMEOUT_SECONDS,
        $streamContext = null
    ) {
        $this->hostname      = $hostname;
        $this->port          = $port;
        $this->timeout       = $timeout;
        $this->streamContext = $streamContext ?: new StreamContext();
    }


    /**
     * @param StreamContext $streamContext
     */
    public function setStreamContext($streamContext)
    {
        $this->streamContext = $streamContext;
    }


    /**
     * @inheritdoc
     */
    public function getRawCertificate()
    {
        try {
            $client = stream_socket_client(
                $this->getRequestUrl(),
                $errorNumber,
                $errorDescription,
                $this->timeout,
                STREAM_CLIENT_CONNECT,
                $this->streamContext->getResource()
            );

            $response = stream_context_get_params($client);

            return $response['options']['ssl']['peer_certificate'];
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();

            // Throw mapped exceptions
            foreach (self::$errorExceptionMap as $needle => $exceptionClass) {
                if (strpos($errorMessage, $needle) !== false) {
                    throw new $exceptionClass($errorMessage);
                }
            }

            throw new ProviderException($errorMessage);
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
