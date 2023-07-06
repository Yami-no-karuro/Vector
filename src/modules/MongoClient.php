<?php

namespace Vector\Module;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Manager;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class MongoClient
{
    protected Client $client;
    protected Manager $manager;
    protected mixed $database;
    private static mixed $instance = null;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct()
    {

        /**
         * @var string $dsn
         * @var string $dbName
         * Use the DSN and the database configured in config.json
         */
        global $config;
        $dbName = $config->mongodb->db_name;
        $dsn = $config->mongodb->dsn;

        $this->client = new Client($dsn);
        $this->manager = $this->client->getManager();
        $this->database = $this->client->$dbName;
    }

    /**
     * @package Vector
     * __destruct()
     */
    public function __destruct()
    {
        $this->manager->close();
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