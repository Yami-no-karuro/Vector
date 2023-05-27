<?php

namespace Vector\Module\ApplicationLogger;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractLogger
{
    protected string $domain;

    /**
     * @package Vector
     * __construct()
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\AbstractLogger->write()
     * @param string $log
     * @return bool
     */
    abstract public function write(string $log): bool;

}
