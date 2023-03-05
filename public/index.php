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
require_once(__DIR__ . '/../src/autoload.php');
require_once(__DIR__ . '/../src/vendor/autoload.php');
require_once(__DIR__ . '/../src/functions.php');
require_once(__DIR__ . '/../src/Router.php');

date_default_timezone_set(DEFAULT_TIMEZONE);

$dir = new RecursiveDirectoryIterator(__DIR__ . '/../src/controllers');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    $fname = $file->getFilename();
    if (preg_match('%\.php$%', $fname)) { 
        require_once ($file->getPathname());
        $controller = 'Vector\\Controllers\\' . basename($fname, '.php'); 
        new $controller; 
    }
}

use Vector\Entities\Response;
$response = new Response(NULL, ['HTTP/1.1 404 Not Found']);
$response->send(true);



