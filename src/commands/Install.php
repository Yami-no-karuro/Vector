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
use Vector\Module\Settings;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Install extends AbstractCommand
{
    protected SqlClient $sql;
    protected FileSystemLogger $logger;

    /**
     * @package Vector
     * __construct()
     * @param array $args
     */
    public function __construct(?array $args)
    {
        parent::__construct($args);
        $this->sql = SqlClient::getInstance();
        $this->logger = new FileSystemLogger('command');
    }

    /**
     * @package Vector
     * Vector\Command\Install->execute()
     * @return int
     */
    public function execute(): int
    {

        /**
         * @var string $dir
         * @var RecursiveDirectoryIterator $sqlDir
         * @var RecursiveIteratorIterator $iterator
         * Iterate through the sql directory, files will be executed as raw sql.
         */
        $dir = Kernel::getProjectRoot() . 'var/sql/';
        if (file_exists($dir) and is_dir($dir)) {
            $sqlDir = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($sqlDir, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $file) {
                $fname = $file->getFilename();
                if (preg_match("%\.sql$%", $fname)) {
                    try {
                        $query = file_get_contents($file->getPathname());
                        $this->sql->exec($query);
                    } catch (Exception $e) {
                        Application::out($e);
                        $this->logger->write($e);
                        return self::EXIT_FAILURE;
                    }
                }
            }
        }

        /**
         * Sets the default settings.   
         */
        try {
            Settings::set('jwt_secret', bin2hex(random_bytes(32)));
        } catch (Exception $e) {
            Application::out($e);
            $this->logger->write($e);
            return self::EXIT_FAILURE;
        }

        Application::out('Vector installed succesfully!');
        return self::EXIT_SUCCESS;
    }

    /**
     * @package Vector
     * Vector\Command\Install->getCommandName()
     * @return void
     */
    public function getCommandName(): string
    {
        return 'vector:install';
    }

}
