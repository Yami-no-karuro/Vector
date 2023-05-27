<?php

namespace Vector\Module\ApplicationLogger;

use Vector\Module\ApplicationLogger\AbstractLogger;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class FileSystemLogger extends AbstractLogger
{
    protected string $path;

    /**
     * @package Vector
     * __construct()
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        parent::__construct($domain);
        $this->path = __DIR__ . '/../../../var/logs/' . $this->domain . '.log.txt';
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\FileSystemLogger->write()
     * @param string $log
     * @return bool
     */
    public function write(string $log): bool
    {
        $log = time() . ' - ' . $log . ' ' . PHP_EOL;
        return @file_put_contents($this->path, $log, FILE_APPEND | LOCK_EX);
    }

}
