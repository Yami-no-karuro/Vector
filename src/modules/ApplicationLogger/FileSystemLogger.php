<?php

namespace Vector\Module\ApplicationLogger;

use Vector\Kernel;
use Vector\Module\ApplicationLogger\AbstractLogger;
use ZipArchive;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class FileSystemLogger extends AbstractLogger
{
    protected string $path;
    protected string $archivePath;

    /**
     * @package Vector
     * __construct()
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        parent::__construct($domain);
        $this->path = Kernel::getProjectRoot() . 'var/logs/' . $this->domain . '.log.txt';
        $this->archivePath = Kernel::getProjectRoot() . 'var/logs/' . $this->domain . '.rotated.zip';
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\FileSystemLogger->write()
     * @param string $log
     * @return bool
     */
    public function write(string $log): bool
    {
        global $config;
        if (filesize($this->path) > $config->debug_log_rotation) {
            $zip = new ZipArchive();
            $zip->open($this->archivePath, ZIPARCHIVE::CREATE);
            $zip->addFile($this->path);
            unlink($this->path);
        }
        $prefix = '[' . date('Y-m-d h:m:s') . ']';
        $log = $prefix . ' ' . $log . ' ' . PHP_EOL;
        return file_put_contents($this->path, $log, FILE_APPEND | LOCK_EX);
    }

}