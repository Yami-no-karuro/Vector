<?php

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

spl_autoload_register(function($class) {
    $pathArr = explode('\\', $class);
    $classname = $pathArr[count($pathArr) - 1];
    if (file_exists(__DIR__ . '/controllers/' . $classname . '.php')) {
        require_once(__DIR__ . '/controllers/' . $classname . '.php');
    } else if (file_exists(__DIR__ . '/modules/' . $classname . '.php')) {
        require_once(__DIR__ . '/modules/' . $classname . '.php');
    } else if (file_exists(__DIR__ . '/objects/' . $classname . '.php')) {
        require_once(__DIR__ . '/objects/' . $classname . '.php');
    } else if (file_exists(__DIR__ . '/events/' . $classname . '.php')) {
        require_once(__DIR__ . '/events/' . $classname . '.php');
    } else if (file_exists(__DIR__ . '/commands/' . $classname . '.php')) {
        require_once(__DIR__ . '/commands/' . $classname . '.php');
    }
});