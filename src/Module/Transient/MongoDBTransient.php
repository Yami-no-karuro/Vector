<?php

namespace Vector\Module\Transient;

use Vector\Module\Transient\AbstractTransient;
use Vector\Module\MongoClient;
use MongoDB\Collection;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class MongoDBTransient extends AbstractTransient
{

    protected Collection $collection;
    protected ?array $content = null;

    /**
     * @package Vector
     * __construct()
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $client = MongoClient::getInstance();
        $this->collection = $client->getCollection('transients');

        if (null !== ($content = $this->collection->findOne(['name' => $this->name]))) {
            $data = $content->getArrayCopy();
            $this->content = [
                'time' => $data['time'],
                'ttl' => $data['ttl'],
                'data' => unserialize($data['data'])
            ];
        }
    }

        /**
     * @package Vector
     * Vector\Module\Transient\MongoDBTransient->isValid()
     * @return bool
     */
    public function isValid(): bool
    {
        if (null === $this->content) {
            return false;
        }
        if ($this->content['ttl'] === 0 or
            time() - $this->content['time'] < $this->content['ttl']) {
            return true;
        }
        return false;
    }

    /**
     * @package Vector
     * Vector\Module\Transient\MongoDBTransient->getData()
     * @return mixed
     */
    public function getData(): mixed
    {
        if (null !== $this->content) {
            return $this->content['data'];
        }
        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Transient\MongoDBTransient->setData()
     * @param mixed $data
     * @param int $ttl
     * @return void
     */
    public function setData(mixed $data, int $ttl = 0): void
    {
        $this->content = [
            'time' => time(),
            'ttl' => $ttl,
            'data' => $data
        ];
        $this->collection->updateOne(
            ['name' => $this->name],
            ['$set' => [
                'time' => time(),
                'ttl' => $ttl,
                'data' => serialize($data)
            ]],
            ['upsert' => true]
        );
    }

    /**
     * @package Vector
     * Vector\Module\Transient\MongoDBTransient->delete()
     * @return void
     */
    public function delete(): void
    {
        $this->collection->deleteOne(['name' => $this->name]);
    }

}
