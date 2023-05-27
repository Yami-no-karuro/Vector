<?php

namespace Vector\Command;

use Vector\Module\Console\AbstractCommand;
use Vector\Module\SqlConnection;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class InstallCommand extends AbstractCommand
{
    protected SqlConnection $sql;

    /**
     * @package Vector
     * __construct()
     * @param array $args
     */
    public function __construct(?array $args)
    {
        parent::__construct($args);
        $this->sql = SqlConnection::getInstance();
    }

    /**
     * @package Vector
     * Vector\Command\InstallCommand->execute()
     * @return int
     */
    public function execute(): int
    {
        $dir = __DIR__ . '/../../var/sql/';
        if (file_exists($dir) and is_dir($dir)) {
            $sqlDir = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($sqlDir, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $file) {
                $fname = $file->getFilename();
                if (preg_match('%\.sql$%', $fname)) {
                    $query = @file_get_contents($file->getPathname());
                    $this->sql->exec($query);
                }
            }
        }
        return 0;
    }

    /**
     * @package Vector
     * Vector\Command\InstallCommand->getCommandName()
     * @return void
     */
    public function getCommandName(): string
    {
        return 'vector:install';
    }

}
