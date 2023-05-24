<?php

namespace Vector\Module\Console;

use Vector\Module\Console\AbstractCommand;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Application
{
    
    protected array $argv;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->run()
     * @return void
     */
    public function run(): void
    {
        $dir = new RecursiveDirectoryIterator(__DIR__ . '/../../commands');
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            $fname = $file->getFilename();
            if (preg_match('%\.php$%', $fname)) {
                $commandClass = 'Vector\\Command\\' . basename($fname, '.php');
                $command = new $commandClass($this->argv);
                $command->setCommand();
                $this->registerCommand($command);
            }
        }
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->registerCommand()
     * @param AbstractCommand $command
     * @return void
     */
    protected function registerCommand(AbstractCommand $command): void
    {
        $args = $command->getArgs();
        if ($args['command'] === $command->getCommand()) {
            $command->execute();
        }
    }

}
