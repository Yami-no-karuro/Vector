<?php

namespace Vector\Module;

use Vector\Module\ApplicationLogger;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class AbstractEvent {

    protected array $args;
    protected ApplicationLogger $applicationLogger;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(array $args)
    {
        $this->args = $args;
        $this->applicationLogger = new ApplicationLogger('events');
    }

    /**
     * @package Vector
     * Vector\Module\AbstractEvent->invoke
     * @return void
     */
    abstract protected function invoke(): void;

}