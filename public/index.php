<?php

/**
* -----------------------------------
* @package Vector
* Vector entrypoint
* -----------------------------------
*/

declare(strict_types = 1);

error_reporting(0);
define('NO_DIRECT_ACCESS', 'true');

require_once(__DIR__ . '/../src/constants.php');
require_once(__DIR__ . '/../src/functions.php');
require_once(__DIR__ . '/../src/autoload.php');
require_once(__DIR__ . '/../vendor/autoload.php');

date_default_timezone_set(DEFAULT_TIMEZONE);

$kernel = new Vector\Kernel();
$kernel->boot();

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

global $request;
$response = new RedirectResponse('/not-found', Response::HTTP_FOUND); 
$response->prepare($request);
$response->send();
die();
