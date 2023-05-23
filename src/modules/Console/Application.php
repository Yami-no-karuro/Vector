<?php

namespace Vector\Module\Console;

use Vector\Module\Console\Command;
use Closure;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Application
{
    protected array $argv;
    protected array $commands = [];

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
     * Vector\Module\Console\Application->registerCommand()
     * @param string $command
     * @param Closure $callback
     * @return void
     */
    public function registerCommand(string $command, Closure $callback): void
    {
        array_push($this->commands, new Command($command, $this->argv, $callback));
    }

    /**
     * @package Vector
     * Vector\Module\Console\Application->run()
     * @return void
     */
    public function run(): void
    {
        foreach ($this->commands as $command) {
            $command->execute();
        }
    }

}
