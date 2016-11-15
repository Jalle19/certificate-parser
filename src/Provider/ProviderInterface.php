<?php

namespace Jalle19\CertificateParser\Provider;

/**
 * Interface ProviderInterface
 * @package Jalle19\CertificateParser\Provider
 */
interface ProviderInterface
{

    /**
     * @return array
     */
    public function getRawCertificate();

}
