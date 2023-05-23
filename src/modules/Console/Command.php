<?php

namespace Vector\Module\Console;

use Closure;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Command {

    protected string $command;
    protected string $console;
    protected array $args;
    protected array $argsSchema = ['command'];
    protected Closure $callback;

    /**
     * @package Vector
     * __construct()
     * @param string $command
     * @param array $argv
     * @param Closure $callback 
     */
    public function __construct(string $command, array $argv, Closure $callback)
    {

        $this->command = $command;
        $this->console = array_shift($argv);
        $this->args = [];
        foreach ($argv as $key => $value) {
            if (isset($this->argsSchema[$key])) {
                $this->args[$this->argsSchema[$key]] = $value; 
            } else { $this->args['args'][] = $value; }
        }
        $this->callback = $callback;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Command->getConsole()
     * @return string
     */    
    public function getConsole(): string
    {
        return $this->console;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Command->getCommand()
     * @return string
     */    
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Command->getArgs()
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Command->execute()
     * @return int
     */
    public function execute(): int
    {
        if ($this->command !== $this->args['command']) { return 1; }
        $callback = $this->callback;
        if (is_callable($callback)) {
            return $callback($this->args); 
        }
    }

}
