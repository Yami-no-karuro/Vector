<?php

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

require_once(__DIR__ . '/Kernel.php');
require_once(__DIR__ . '/modules/Transient/AbstractTransient.php');
require_once(__DIR__ . '/modules/Transient/FileSystemTransient.php');

use Vector\Kernel;
use Vector\Module\Transient\FileSystemTransient;

spl_autoload_register(function ($class) {
    $classId = strtolower(str_replace('\\', '-', $class));
    $transient = new FileSystemTransient('vct-autoload-{' . $classId . '}');
    if ($transient->isValid()) {
        require_once($transient->getData());
        return;
    }
    $pathArr = explode('\\', $class);
    $classname = $pathArr[count($pathArr) - 1];
    $dir = new RecursiveDirectoryIterator(Kernel::getProjectRoot() . 'src');
    $iterator = new RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
        if (str_contains($file->getFilename(), $classname)) {
            $transient->setData($file->getPathname());
            require_once($file->getPathname());
        }
    }
});
