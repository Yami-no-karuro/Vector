<?php

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

require_once(__DIR__ . '/Kernel.php');
use Vector\Kernel;

spl_autoload_register(function ($class) {

    /**
     * @var array $namespace
     * The full namespace is shifted to get the project scr folder as root.
     */
    $namespace = explode('\\', $class);
    array_shift($namespace);
   
    /**
     * @var string $filepath
     * The actual filepath is built and required.
     */
    $filepath = Kernel::getProjectRoot() . 'src/' . implode('/', $namespace) . '.php';
    if (file_exists($filepath)) {
        require_once($filepath);
    }
});
