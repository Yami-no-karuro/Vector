<?php

namespace Vector\Module;

use MongoDB\Client;
use MongoDB\Collection;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class MongoClient
{

    protected Client $client;
    protected mixed $database;
    private static mixed $instance = null;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct()
    {
        global $config;
        $dbName = $config->mongodb->db_name;
        $dsn = $config->mongodb->dsn;

        $this->client = new Client($dsn);
        $this->database = $this->client->$dbName;
    }

    /**
     * @package Vector
     * Vector\Module\MongoClient::getInstance()
     * @return MongoClient
     */
    public static function getInstance(): MongoClient
    {
        if (self::$instance == null) {
            self::$instance = new MongoClient();
        }

        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Module\MongoClient->getCollection()
     * @param string $collection
     * @return Collection
     */
    public function getCollection(string $collection): Collection
    {
        return $this->database->$collection;
    }

}
