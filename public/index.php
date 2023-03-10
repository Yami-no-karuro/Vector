<?php

/**
* -----------------------------------
* @package Vector
* Vector entrypoint
* -----------------------------------
*/

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('NO_DIRECT_ACCESS', 'true');

require_once(__DIR__ . '/../src/config.php');
require_once(__DIR__ . '/../src/autoload.php');
require_once(__DIR__ . '/../src/vendor/autoload.php');
require_once(__DIR__ . '/../src/functions.php');
require_once(__DIR__ . '/../src/Router.php');

date_default_timezone_set(DEFAULT_TIMEZONE);

$dir = new RecursiveDirectoryIterator(__DIR__ . '/../src/Controller');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    $fname = $file->getFilename();
    if (preg_match('%\.php$%', $fname)) { 
        require_once ($file->getPathname());
        $controller = 'Vector\\Controller\\' . basename($fname, '.php'); 
        new $controller; 
    }
}

$request = Request::createFromGlobals();
$response = new Response('', Response::HTTP_NOT_FOUND);
$response->prepare($request);
$response->send();
die();



