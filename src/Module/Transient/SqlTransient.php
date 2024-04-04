<?php

namespace Vector\Module\Transient;

use Vector\Module\Transient\AbstractTransient;
use Vector\Module\SqlClient;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SqlTransient extends AbstractTransient
{

    protected PDO $sql;
    protected ?array $content = null;

    /**
     * @package Vector
     * __construct()
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->sql = SqlClient::getInstance()
            ->getClient();

        $query = "SELECT `content` FROM `vct_transients` WHERE `name` = :name LIMIT 1";
        $q = $this->sql->prepare($query);

        $q->bindParam('name', $name, PDO::PARAM_STR);
        $q->execute();

        if (false !== ($result = $q->fetch(PDO::FETCH_ASSOC))) {
            $this->content = unserialize($result['content']);
        }
    }

    /**
     * @package Vector
     * Vector\Module\Transient\SqlTransient->isValid()
     * @return bool
     */
    public function isValid(): bool
    {
        if (null !== $this->content &&
            ($this->content['ttl'] === 0 ||
            time() - $this->content['time'] < $this->content['ttl'])) {
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

        $query = "INSERT INTO `vct_transients` (`name`, `content`) VALUES (:name, :content) ON DUPLICATE KEY UPDATE `content` = :content";
        $q = $this->sql->prepare($query);

        $q->bindParam('name', $this->name, PDO::PARAM_STR);
        $q->bindParam('content', $serialized, PDO::PARAM_STR);
        $q->execute();
    }

    /**
     * @package Vector
     * Vector\Module\Transient\SqlTransient->delete()
     * @return void
     */
    public function delete(): void
    {
        $query = "DELETE FROM `vct_transients` WHERE `name` = :name";
        $q = $this->sql->prepare($query);

        $q->bindParam('name', $this->name, PDO::PARAM_STR);
        $q->execute();
    }

}
