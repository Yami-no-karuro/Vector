<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class ApplicationLogger {

    protected string $filepath;

    /**
     * @package Vector
     * @param string $type
     * __construct()
     */
    public function __construct(string $type) 
    {
        $this->filepath = __DIR__ . '/../var/logs/' . $type . '.log.txt';
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger->writeLog()
	 * @param string $content
     * @return bool
     */
    public function writeLog(string $content): bool 
    {
        return @file_put_contents($this->filepath, $content);
    }

}