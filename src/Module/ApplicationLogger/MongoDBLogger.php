<?php

namespace Vector\Module\ApplicationLogger;

use Vector\Module\ApplicationLogger\AbstractLogger;
use Vector\Module\MongoClient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class MongoDBLogger extends AbstractLogger
{

    protected MongoClient $client;

    /**
    * @package Vector
    * __construct()
    * @param string $type
    */
    public function __construct(string $type)
    {
        parent::__construct($type);
        $this->client = MongoClient::getInstance();
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\MongoDBLogger->write()
     * @param string $log
     * @return void
     */
    public function write(string $log): void
    {
        $collection = $this->client->getCollection('logs');
        $collection->insertOne([
            'domain' => $this->domain,
            'time' => time(),
            'log' => $log
        ]);
    }

}
