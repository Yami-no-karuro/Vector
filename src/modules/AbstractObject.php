<?php

namespace Vector\Module;

use Vector\Module\SqlConnection;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\ApplicationLogger\SqlLogger;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class AbstractObject {

    protected SqlConnection $sql;
    protected FileSystemLogger|SqlLogger $applicationLogger;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->sql = SqlConnection::getInstance();
        if (true === DATABASE_LOGS) {
            $this->applicationLogger = new SqlLogger('object');
        } else { $this->applicationLogger = new FileSystemLogger('object'); }
    }

}
