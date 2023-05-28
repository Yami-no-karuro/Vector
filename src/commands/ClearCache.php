<?php

namespace Vector\Command;

use Vector\Module\Console\AbstractCommand;
use Vector\Module\SqlClient;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class ClearCache extends AbstractCommand
{
    protected SqlClient $sql;

    /**
     * @package Vector
     * __construct()
     * @param array $args
     */
    public function __construct(?array $args)
    {
        parent::__construct($args);
        $this->sql = SqlClient::getInstance();
    }

    /**
     * @package Vector
     * Vector\Command\ClearCache->execute()
     * @return int
     */
    public function execute(): int
    {
        $this->sql->exec('DELETE FROM `transients`');
        $dir = __DIR__ . '/../../var/cache/';
        if (file_exists($dir) and is_dir($dir)) {
            $cacheDir = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($cacheDir, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $file) {
                $fname = $file->getFilename();
                if (!preg_match('%\.gitkeep$%', $fname)) {
                    $file->isDir() ? rmdir($file) : unlink($file);
                }
            }
        }
        return 0;
    }

    /**
     * @package Vector
     * Vector\Command\ClearCache->getCommandName()
     * @return void
     */
    public function getCommandName(): string
    {
        return 'vector:clear-cache';
    }

}
