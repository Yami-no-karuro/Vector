<?php

namespace Vector\Command;

use Vector\DataObject\User;
use Vector\Module\Console\AbstractCommand;
use Vector\Module\SqlClient;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\Console\Application;
use Vector\Module\Settings;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Install extends AbstractCommand
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
        $this->sql = SqlClient::getInstance()
            ->getClient();

        $this->logger = new FileSystemLogger('command');
    }

    /**
     * @package Vector
     * Vector\Command\Install->execute()
     * @return int
     */
    public function execute(): int
    {

        $dir = getProjectRoot() . 'var/sql/';
        if (file_exists($dir) && is_dir($dir)) {
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

        Settings::set('installed', true);
        Settings::set('web_token_secret', bin2hex(random_bytes(32)));
        Settings::set('web_token_ttl', 3600);
        Settings::set('crypt_key', bin2hex(random_bytes(32)));
        Settings::set('crypt_iv', bin2hex(random_bytes(8)));

        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setPassword('Administrator');
        $admin->setUsername('Admin');
        $admin->save();

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
