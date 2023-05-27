<?php

namespace Vector\Module;

use Predis\Client;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class RedisConnection
{

    protected Client $client;
    private static mixed $instance = null;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        global $config;
        $this->client = new Client([
            'scheme' => $config->redis->scheme,
            'host' => $config->redis->host,
            'port' => $config->redis->port
        ]);
    }

    /**
     * @package Vector
     * __destruct()
     */
    public function __destruct()
    {
        $this->client->disconnect();
    }

    /**
     * @package Vector
     * Vector\Module\RedisConnection::getInstance()
     * @return RedisConnection
     */
    public static function getInstance(): RedisConnection
    {
        if (self::$instance == null) {
            self::$instance = new RedisConnection();
        }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Module\RedisConnection->set()
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @return void
     */
    public function set(string $key, string $value, int $ttl): void
    {
        $this->client->set($key, $value, 'EX', $ttl);
    }

    /**
     * @package Vector
     * Vector\Module\RedisConnection->set()
     * @param string $key
     * @return ?string
     */
    public function get(string $key): ?string
    {
        return $this->client->get($key);
    }

    /**
     * @package Vector
     * Vector\Module\RedisConnection->delete()
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        $this->client->del($key);
    }

    /**
     * @package Vector
     * Vector\Module\RedisConnection->exists()
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->client->exists($key);
    }

}