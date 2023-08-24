<?php

namespace Vector\Module\Console;

use Vector\Kernel;
use Vector\Module\Transient\FileSystemTransient;
use Vector\Module\Transient\SqlTransient;
use Vector\Module\StopWatch;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Application
{
    protected SqlTransient|FileSystemTransient $transient;
    protected StopWatch $stopWatch;
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
        $this->command = '';
        if (!empty($argv)) {
            $this->command = array_shift($argv);
        }

        $this->loadConfig();
        try {
            $this->transient = new SqlTransient('vct-command-{' . $this->command . '}');
        } catch (Exception) {
            $this->transient = new FileSystemTransient('vct-command-{' . $this->command . '}');
            self::out('Temporarely saving command transients on filesystem, please run the "vector:cache-clear" command once installation is complete.');
        }
        $this->stopWatch = new StopWatch();
        $this->args = $argv;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->run()
     * @return void
     */
    public function run(): void
    {
        $this->directRun();

        /**
         * @var array $registeredCommand
         * @var RecursiveDirectoryIterator $dir
         * @var RecursiveIteratorIterator $iterator
         * Iterate thorough command classes looking for registered commands.
         * If nothing is found matching the given criteria prints out the available commands.
         */
        $registeredCommands = [];
        $dir = new RecursiveDirectoryIterator(Kernel::getProjectRoot() . 'src/commands');
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            $fname = $file->getFilename();
            if (preg_match("%\.php$%", $fname)) {

                /**
                 * @var string $class
                 * @var AbstractCommand $command
                 * @var string $commandName
                 * @var array $registeredCommands
                 * Initialize class to retrive command informations.
                 * If the current input matches the command will be executed.
                 * Command data is cached for future runs.
                 */
                $class = 'Vector\\Command\\' . basename($fname, '.php');
                $command = new $class($this->args);
                $commandName = $command->getCommandName();
                $registeredCommands[] = $commandName;
                if ($commandName === $this->command) {
                    $this->transient->setData([
                        'command' => $this->command,
                        'handler' => $class
                    ]);
                    $this->stopWatch->start();
                    $exitCode = $command->execute();
                    $this->stopWatch->stop();
                    self::out('Exitcode: ' . $exitCode);
                    self::out('Executed for: ' . $this->stopWatch->getElapsed());
                    exit($exitCode);
                }

            }
        }

        $this->vectorCliSignature();
        self::out('Unable to find command: "' . $this->command . '"');
        self::out('Available commands:');
        foreach ($registeredCommands as $registeredCommand) {
            self::out('"' . $registeredCommand . '"');
        }
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->directRun()
     * @return void
     */
    protected function directRun(): void
    {

        /**
         * @var string $cache
         * @var string $class
         * @var AbstractCommand $command
         */
        if ($this->transient->isValid(HOUR_IN_SECONDS)) {
            $cache = $this->transient->getData();
            $class = $cache['handler'];
            $command = new $class($this->args);
            if ($command->getCommandName() === $cache['command']) {
                $this->stopWatch->start();
                $exitCode = $command->execute();
                $this->stopWatch->stop();
                self::out('Exitcode: ' . $exitCode);
                self::out('Executed for: ' . $this->stopWatch->getElapsed());
                exit($exitCode);
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
        if ($transient->isValid()) {
            $data = $transient->getData();
        } else {
            $path = Kernel::getProjectRoot() . 'config/config.json';
            $data = json_decode(file_get_contents($path));
            $transient->setData($data);
        }
        $config = $data;

    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->vectorCliSignature()
     * @return void
     */
    protected function vectorCliSignature(): void
    {
        print_r('
                _             
/\   /\___  ___| |_ ___  _ __ 
\ \ / / _ \/ __| __/ _ \| \'__|
 \ V /  __/ (__| || (_) | |   
  \_/ \___|\___|\__\___/|_|   
                By Yami-no-karuro          
        ');
        echo PHP_EOL;
        echo '---------------------------------';
        echo PHP_EOL . PHP_EOL;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application::out()
     * @param mixed $message
     * @return void
     */
    public static function out(mixed $message): void
    {
        print_r($message);
        echo PHP_EOL;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application::in()
     * @param string $outMessage
     * @return string
     */
    public static function in(string $outMessage): string
    {
        self::out($outMessage);
        $handle = fopen('php://stdin', 'r');
        return trim(fgets($handle));
    }

}
