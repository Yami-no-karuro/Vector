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
     * Vector\Module\ApplicationLogger\AbstractLogger->getDomain()
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\AbstractLogger->setDomain()
     * @param string $domain
     * @return void
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\AbstractLogger->write()
     * @param string $log
     * @return void
     */
    abstract public function write(string $log): void;
}
