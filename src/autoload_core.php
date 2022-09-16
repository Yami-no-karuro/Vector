<?php

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    echo '403 Forbidden';
    die(); 
}

spl_autoload_register(function($class) {
    $path_arr = explode('\\', $class);
    $classname = $path_arr[count($path_arr) - 1];
    if (file_exists(__DIR__ . '/functions/' . $classname . '.php')) {
        require_once(__DIR__ . '/functions/' . $classname . '.php');
    } else if (file_exists(__DIR__ . '/objects/' . $classname . '.php')) {
        require_once(__DIR__ . '/objects/' . $classname . '.php');
    } else if (file_exists(__DIR__ . '/controllers/' . $classname . '.php')) {
        require_once(__DIR__ . '/controllers/' . $classname . '.php');
    }
});