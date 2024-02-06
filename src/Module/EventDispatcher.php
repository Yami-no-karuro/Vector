<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class EventDispatcher
{

    /**
     * @package Vector
     * Vector\Module\EventDispatcher::dispatch()
     * @param string $eventClass
     * @param string $eventMethod
     * @param array $args
     * @return void
     */
    public static function dispatch(string $eventClass, string $eventMethod, array $args): void
    {
        $eventClass = 'Vector\\Event\\' . $eventClass;
        if (class_exists($eventClass)) {
            $event = new $eventClass();
            if (method_exists($event, $eventMethod)) {
                $event->$eventMethod(...$args);
            }
        }
    }

}
