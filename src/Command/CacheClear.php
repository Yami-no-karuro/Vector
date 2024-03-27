<?php

namespace Vector\Command;

use Vector\Kernel;
use Vector\Module\Console\AbstractCommand;
use Vector\Module\SqlClient;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\Console\Application;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class CacheClear extends AbstractCommand
{

    protected PDO $sql;
    protected FileSystemLogger $logger;

    /**
     * @package Vector
     * __construct()
     * @param array $args
     */
    public function __construct(?array $args)
    {
        parent::__construct($args);

        $this->logger = new FileSystemLogger('command');
        $this->sql = SqlClient::getInstance()
            ->getClient();
    }

    /**
     * @package Vector
     * Vector\Command\CacheClear->execute()
     * @return int
     */
    public function execute(): int
    {

        try {
            $this->sql->exec('DELETE FROM `transients`');
        } catch (Exception $e) {
            Application::out($e);
            $this->logger->write($e);
            return self::EXIT_FAILURE;
        }

        $dir = getProjectRoot() . 'var/cache/';
        if (file_exists($dir) && is_dir($dir)) {
            $cacheDir = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($cacheDir, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $file) {
                $fname = $file->getFilename();
                if (!preg_match("%\.gitkeep$%", $fname)) {
                    try {
                        $file->isDir() ? rmdir($file) : unlink($file);
                    } catch (Exception $e) {
                        Application::out($e);
                        $this->logger->write($e);
                        return self::EXIT_FAILURE;
                    }
                }
            }
        }

        Application::out('Cache cleared successfully!');
        return self::EXIT_SUCCESS;
    }

    /**
     * @package Vector
     * Vector\Command\CacheClear->getCommandName()
     * @return void
     */
    public function getCommandName(): string
    {
        return 'vector:cache-clear';
    }

}
