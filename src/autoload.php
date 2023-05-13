<?php

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

spl_autoload_register(function($class) {
    $pathArr = explode('\\', $class);
    $classname = $pathArr[count($pathArr) - 1];
    $dir = new RecursiveDirectoryIterator(__DIR__ . '/../src');
    $iterator = new RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
        if (str_contains($file->getFilename(), $classname)) {
            require_once($file->getPathname());
        }
    }
});