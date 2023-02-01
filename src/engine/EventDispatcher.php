<?php
namespace Vector\Engine;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class EventDispatcher {

    protected string $event_name;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(string $event_name) {
       $this->event_name = $event_name;
    }
    
    /**
     * @package Vector
     * Vector\Engine\EventDispatcher->dispatch()
     * @param {array} $args
     * @return void
     */
    public function dispatch(array $args): void {
        $classname = "Vector\\Events\\{$this->event_name}";
        if (class_exists($classname)) {
            $event = new $classname($args); 
        }
    }

}
