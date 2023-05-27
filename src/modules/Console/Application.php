<?php

namespace Vector\Module\Console;

use Vector\Module\Transient\FileSystemTransient;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Application
{
    protected FileSystemTransient $transient;
    protected string $console;
    protected string $command;
    protected ?array $args;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(array $argv)
    {
        $this->console = array_shift($argv);
        $this->command = array_shift($argv);
        $this->transient = new FileSystemTransient('vct-command-{' . $this->command . '}');
        $this->args = $argv;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->getConsole()
     * @return string
     */
    public function getConsole(): string
    {
        return $this->console;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->getCommand()
     * @return ?string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->getArgs()
     * @return ?array
     */
    public function getArgs(): ?array
    {
        return $this->args;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->run()
     * @return void
     */
    public function run(): void
    {
        $this->loadConfig();
        $this->directRun();
        $dir = new RecursiveDirectoryIterator(__DIR__ . '/../../commands');
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            $fname = $file->getFilename();
            if (preg_match('%\.php$%', $fname)) {
                $class = 'Vector\\Command\\' . basename($fname, '.php');
                $command = new $class($this->args);
                if ($command->getCommandName() === $this->command) {
                    $this->transient->setData([
                        'command' => $this->command,
                        'handler' => $class
                    ]);
                    $command->execute();
                    exit(0);
                }
            }
        }

    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->directRun()
     * @return void
     */
    protected function directRun(): void
    {
        if ($this->transient->isValid(3600)) {
            $cache = $this->transient->getData();
            $class = $cache['handler'];
            $command = new $class($this->args);
            if ($command->getCommandName() === $cache['command']) {
                $command->execute();
                exit(0);
            }
        }
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->loadConfig()
     * @return void
     */
    protected function loadConfig(): void
    {

        /**
         * @var FileSystemTransient $transient
         * @var object $config
         */
        global $config;
        $transient = new FileSystemTransient('vct-config');
        if ($transient->isValid(3600)) {
            $data = $transient->getData();
        } else {
            $path = __DIR__ . '/../../../config/config.json';
            $data = json_decode(@file_get_contents($path));
            $transient->setData($data);
        }
        $config = $data;

    }

}
