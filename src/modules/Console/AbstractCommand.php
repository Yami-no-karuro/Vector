<?php

namespace Vector\Module\Console;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractCommand
{
    protected ?string $command;
    protected string $console;
    protected array $args;
    protected array $argsSchema = ['command'];

    /**
     * @package Vector
     * __construct()
     * @param array $argv
     */
    public function __construct(array $argv)
    {
        $this->console = array_shift($argv);
        $this->args = [];
        foreach ($argv as $key => $value) {
            if (isset($this->argsSchema[$key])) {
                $this->args[$this->argsSchema[$key]] = $value;
            } else {
                $this->args['args'][] = $value;
            }
        }
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
    abstract public function execute(): int;

    /**
     * @package Vector
     * Vector\Module\Console\Command->setCommand()
     * @return void
     */
    abstract public function setCommand(): void;

}
