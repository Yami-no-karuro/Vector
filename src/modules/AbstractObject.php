<?php

namespace Vector\Module;

use Vector\Module\SqlConnection;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class AbstractObject {

    protected SqlConnection $sql;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->sql = SqlConnection::getInstance();
    }

}
