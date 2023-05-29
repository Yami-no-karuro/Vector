<?php

/**
* -----------------------------------
* @package Vector
* Vector entrypoint
* -----------------------------------
*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('NO_DIRECT_ACCESS', 'true');

require_once(__DIR__ . '/../src/constants.php');
require_once(__DIR__ . '/../src/functions.php');
require_once(__DIR__ . '/../src/autoload.php');
require_once(__DIR__ . '/../vendor/autoload.php');

date_default_timezone_set(DEFAULT_TIMEZONE);

$kernel = new Vector\Kernel();
$kernel->boot();

use Symfony\Component\HttpFoundation\Response;

global $request;
$response = new Response(null, Response::HTTP_NOT_FOUND);
$response->prepare($request);
$response->send();
die();


/*

set_error_handler(function() {
    ini_set('display_errors', 'off');
    header('HTTP/1.1 500 Internal Server Error');
});

*/

