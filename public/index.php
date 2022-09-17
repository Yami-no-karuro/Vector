<?php

/**
* -----------------------------------
* @package Vector
* Vector entrypoint
* -----------------------------------
*/

session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('NO_DIRECT_ACCESS', 'true');

require_once(__DIR__ . '/../src/config.php');
require_once(__DIR__ . '/../src/autoload_core.php');
require_once(__DIR__ . '/../src/Router.php');

date_default_timezone_set(DEFAULT_TIMEZONE);

$dir = new RecursiveDirectoryIterator(__DIR__ . '/../src/controllers');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    $fname = $file->getFilename();
    if (preg_match('%\.php$%', $fname)) {
        require_once ($file->getPathname());
    }
}

header('HTTP/1.1 404 Not Found');
echo '404 Not Found';



