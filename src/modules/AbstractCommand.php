<?php

namespace Vector\Module;

use Vector\Module\ApplicationLogger\FileSystemLogger;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class AbstractCommand {

    protected string $console;
    protected array $args;
    protected array $argsSchema = ['command'];
    protected FileSystemLogger $applicationLogger;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(array $argv)
    {

        /** 
         * @var FileSystemLogger $applicationLogger 
         * @var string $console
         * @var array $args
         */
        $this->applicationLogger = new FileSystemLogger('command');
        $this->console = array_shift($argv);
        $this->args = $argv;

    }

    /**
     * @package Vector
     * Vector\Module\AbstractCommand->getConsole()
     * @return string
     */
    public function getConsole(): string
    {
        return $this->console;
    }

    /**
     * @package Vector
     * Vector\Module\AbstractCommand->getArgs()
     * @return array
     */
    public function getArgs(): array
    {
        $args = [];
        foreach ($this->args as $key => $value) {
            if (!isset($this->argsSchema[$key])) {
                $args['flags'][] = $value;
            } else { $args[$this->argsSchema[$key]] = $value; }
        }
        return $args;
    }

    /**
     * @package Vector
     * Vector\Module\AbstractCommand->exec()
     * @return int
     */
    abstract public function exec(): int;

}