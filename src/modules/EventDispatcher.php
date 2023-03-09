<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class EventDispatcher {

    protected string $eventName;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(string $eventName) 
    {
       $this->eventName = $eventName;
    }
    
    /**
     * @package Vector
     * Vector\Module\EventDispatcher->dispatch()
     * @param array $args
     * @return void
     */
    public function dispatch(array $args): void 
    {
        $classname = "Vector\\Event\\{$this->eventName}";
        if (class_exists($classname)) { new $classname(...$args); }
    }

}
