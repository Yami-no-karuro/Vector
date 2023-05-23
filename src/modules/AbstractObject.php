<?php

namespace Vector\Module;

use Vector\Module\SqlConnection;
use Vector\Module\ApplicationLogger\FileSystemLogger;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractObject
{
    protected SqlConnection $sql;
    protected FileSystemLogger $applicationLogger;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {

        /**
         * @var SqlConnection $sql
         * @var FileSystemLogger $applicationLogger
         */
        $this->sql = SqlConnection::getInstance();
        $this->applicationLogger = new FileSystemLogger('object');

    }

}
