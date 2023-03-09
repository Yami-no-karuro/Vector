<?php

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

spl_autoload_register(function($class) {
    $path_arr = explode('\\', $class);
    $classname = $path_arr[count($path_arr) - 1];
    if (file_exists(__DIR__ . '/modules/' . $classname . '.php')) {
        require_once(__DIR__ . '/modules/' . $classname . '.php');
    } else if (file_exists(__DIR__ . '/events/' . $classname . '.php')) {
        require_once(__DIR__ . '/events/' . $classname . '.php');
    }
});