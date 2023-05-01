<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class EventDispatcher {

    /**
     * @package Vector
     * @param string $event
     * __construct()
     */
    public function __construct(
        protected string $event,
        protected array $args
    ) {}

    /**
     * @package Vector
     * Vector\Module\EventDispatcher->dispatch()
	 * @param string $content
     * @return bool
     */
    public function dispatch(): void
    {
        $eventClass = '\\Vector\\Event\\' . $this->event;
        if (class_exists($eventClass)) {
            new $eventClass(...$this->args);
        }
    }

}