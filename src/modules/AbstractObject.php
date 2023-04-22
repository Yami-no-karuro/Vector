<?php

namespace Vector\Module;

use Vector\Module\SqlConnection;
use Vector\Module\ApplicationLogger;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class AbstractObject {

    protected SqlConnection $sql;
    protected ApplicationLogger $applicationLogger;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->sql = SqlConnection::getInstance();
        $this->applicationLogger = new ApplicationLogger('objects');
    }

}
