<?php

namespace Vector\Module\ApplicationLogger;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractLogger
{
    /**
     * @package Vector
     * __construct()
     * @param string $type
     */
    public function __construct(protected string $type)
    {
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\AbstractLogger->write()
     * @param string $content
     * @return bool
     */
    abstract public function write(string $content): bool;

}
