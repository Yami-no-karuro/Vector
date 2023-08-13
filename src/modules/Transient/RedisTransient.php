<?php

namespace Vector\Module\Transient;

use Vector\Module\RedisClient;
use Vector\Module\Transient\AbstractTransient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class RedisTransient extends AbstractTransient
{
    protected RedisClient $client;
    protected mixed $content = null;

    /**
     * @package Vector
     * __construct()
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->client = RedisClient::getInstance();
        if (null !== ($data = $this->client->get($this->name))) {
            $this->content = unserialize($data);
        }

    }

    /**
     * @package Vector
     * Vector\Module\Transient\RedisTransient->isValid()
     * @return bool
     */
    public function isValid(): bool
    {
        if (null === $this->content) {
            return false;
        }
        return true;
    }

    /**
     * @package Vector
     * Vector\Module\Transient\RedisTransient->getData()
     * @return mixed
     */
    public function getData(): mixed
    {
        if (null !== $this->content) {
            return $this->content;
        }
        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Transient\RedisTransient->setData()
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function setData(mixed $data, int $ttl = 0): void
    {
        $this->content = $data;
        $serialized = serialize($data);
        $this->client->set($this->name, $serialized, $ttl);
    }

    /**
     * @package Vector
     * Vector\Module\Transient\RedisTransient->delete()
     * @return bool
     */
    public function delete(): void
    {
        $this->client->delete($this->name);
    }

}
