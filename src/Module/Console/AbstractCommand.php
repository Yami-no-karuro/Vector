<?php

namespace Vector\Module\Console;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractCommand
{

    public const EXIT_SUCCESS = 0;
    public const EXIT_FAILURE = 1;

    protected ?array $args;

    /**
     * @package Vector
     * __construct()
     * @param array $args
     */
    public function __construct(?array $args)
    {
        $this->args = $args;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Command->getArgs()
     * @return ?array
     */
    protected function getArgs(): ?array
    {
        return $this->args;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Command->setArgs()
     * @param ?array $args
     * @return void
     */
    protected function setArgs(?array $args): void
    {
        $this->args = $args;
    }

    /**
     * @package Vector
     * Vector\Module\Console\Command->execute()
     * @return int
     */
    abstract public function execute(): int;

    /**
     * @package Vector
     * Vector\Module\Console\Command->getCommandName()
     * @return string
     */
    abstract public function getCommandName(): string;

}
