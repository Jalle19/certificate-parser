<?php

namespace Jalle19\CertificateParser\Provider;

/**
 * Interface ProviderInterface
 * @package Jalle19\CertificateParser\Provider
 */
interface ProviderInterface
{

    /**
     * @return resource the raw X.509 certificate
     */
    public function getRawCertificate();

}
