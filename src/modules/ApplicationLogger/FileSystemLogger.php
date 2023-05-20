<?php

namespace Vector\Module\ApplicationLogger;

use Vector\Module\ApplicationLogger\AbstractLogger;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class FileSystemLogger extends AbstractLogger {

    protected string $path;

    /**
     * @package Vector
     * @param string $type
     * __construct()
     */
    public function __construct(string $type)
    {
        parent::__construct($type);
        $this->path = __DIR__ . '/../../../var/logs/' . $this->type . '.log.txt';
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\FileSystemLogger->writeLog()
	 * @param string $content
     * @return bool
     */
    public function write(string $content): bool 
    {
        return @file_put_contents($this->path, $content . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

}