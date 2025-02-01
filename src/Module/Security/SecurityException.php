<?php

namespace Vector\Module\Security;

use Exception;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SecurityException extends Exception {}
