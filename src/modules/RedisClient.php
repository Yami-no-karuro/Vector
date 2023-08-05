<?php

namespace Vector\Module;

use Predis\Client;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class RedisClient
{
    protected Client $client;
    private static mixed $instance = null;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct()
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
     * Vector\Module\RedisClient::getInstance()
     * @return RedisClient
     */
    public static function getInstance(): RedisClient
    {
        if (self::$instance == null) {
            self::$instance = new RedisClient();
        }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Module\RedisClient->set()
     * @param string $key
     * @param string $value
     * @param int $ttl
     * @return void
     */
    public function set(string $key, string $value, int $ttl = 86400): void
    {
        $this->client->set($key, $value, 'EX', $ttl);
    }

    /**
     * @package Vector
     * Vector\Module\RedisClient->set()
     * @param string $key
     * @return ?string
     */
    public function get(string $key): ?string
    {
        return $this->client->get($key);
    }

    /**
     * @package Vector
     * Vector\Module\RedisClient->scan()
     * @param mixed $cursor
     * @param ?array $options
     * @return array
     */
    public function scan(mixed $cursor = null, ?array $options = null): array
    {
        $scan = $this->client->scan($cursor, $options);
        return ['cursor' => $scan[0], 'keys' => $scan[1]];
    }

    /**
     * @package Vector
     * Vector\Module\RedisClient->delete()
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        $this->client->del($key);
    }

    /**
     * @package Vector
     * Vector\Module\RedisClient->flush()
     * @return void
     */
    public function flush(): void
    {
        $this->client->flushall();
    }

    /**
     * @package Vector
     * Vector\Module\RedisClient->exists()
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->client->exists($key);
    }

}
