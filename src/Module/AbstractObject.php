<?php

namespace Vector\Module;

use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractObject
{

    protected PDO $sql;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(array $data = [])
    {
        $this->sql = SqlClient::getInstance()
            ->getClient();

        foreach (array_keys($data) as $key) {
            $this->$key = $data[$key];
        }
    }

    /**
     * @package Vector
     * Vector\Module\AbstractObject->get()
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return isset($this->$key) ? $this->$key : null;
    }

    /**
     * @package Vector
     * Vector\Module\AbstractObject->save()
     * @return void 
     */
    abstract public function save(): void;

    /**
     * @package Vector
     * Vector\Module\AbstractObject->delete()
     * @return void 
     */
    abstract public function delete(): void;

}