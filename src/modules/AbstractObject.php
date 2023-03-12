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

    /**
     * @package Vector
     * Vector\Model\AbstractObject::create()
     * @return AbstractObject
     */
    abstract public static function create(); 

    /**
     * @package Vector
     * Vector\Module\AbstractObject->save
     * @return bool
     */
    abstract public function save();

    /**
     * @package Vector
     * Vector\Module\AbstractObject->delete
     * @return bool
     */
    abstract public function delete();

}