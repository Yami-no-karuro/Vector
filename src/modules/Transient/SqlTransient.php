<?php

namespace Vector\Module\Transient;

use Vector\Module\Transient\AbstractTransient;
use Vector\Module\SqlClient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SqlTransient extends AbstractTransient
{
    protected SqlClient $sql;
    protected ?array $content = null;

    /**
     * @package Vector
     * __construct()
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);

        /**
         * @var array $transient
         * If the transient data is available content is unserialized.
         */
        $this->sql = SqlClient::getInstance();
        $transient = $this->sql->getResults("SELECT `content` 
            FROM `transients` 
            WHERE `name` = ? LIMIT 1", [
                ['type' => 's', 'value' => $this->name]
        ]);
        if (true === $transient['success'] and !empty($transient['data'])) {
            $this->content = unserialize($transient['data']['content']);
        }

    }

    /**
     * @package Vector
     * Vector\Module\Transient\SqlTransient->isValid()
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
     * Vector\Module\Transient\SqlTransient->getData()
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
     * Vector\Module\Transient\SqlTransient->setData()
     * @param mixed $data
     * @param int $ttl
     * @return void
     */
    public function setData(mixed $data, int $ttl = 0): void
    {
        $content = [
            'time' => time(),
            'ttl' => $ttl,
            'data' => $data
        ];
        $this->content = $content;
        $serialized = serialize($content);
        $this->sql->exec("INSERT INTO `transients` 
            (`ID`, `name`, `content`) VALUES (NULL, ?, ?) 
            ON DUPLICATE KEY UPDATE `content` = ?", [
                ['type' => 's', 'value' => $this->name],
                ['type' => 's', 'value' => $serialized],
                ['type' => 's', 'value' => $serialized]
        ]);
    }

    /**
     * @package Vector
     * Vector\Module\Transient\SqlTransient->delete()
     * @return void
     */
    public function delete(): void
    {
        $this->sql->exec("DELETE FROM `transients` WHERE `name` = ?", [
            ['type' => 's', 'value' => $this->name]
        ]);
    }

}
