<?php

/**
* -----------------------------------
* @package Vector
* Vector entrypoint
* -----------------------------------
*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('NO_DIRECT_ACCESS', 'true');

require_once(__DIR__ . '/../src/config.php');
require_once(__DIR__ . '/../src/autoload.php');
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../src/functions.php');
require_once(__DIR__ . '/../src/Kernel.php');
require_once(__DIR__ . '/../src/Router.php');

date_default_timezone_set(DEFAULT_TIMEZONE);

$kernel = new Vector\Kernel();
$kernel->boot();

header('HTTP/1.1 404 Not Found');
die(); 



