<?php

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

require_once(__DIR__ . '/functions.php');

spl_autoload_register(function ($class) {
    $namespace = explode('\\', $class);
    array_shift($namespace);

    $filepath = getProjectRoot() . 'src/' . implode('/', $namespace) . '.php';
    if (file_exists($filepath))
        require_once($filepath);
});
