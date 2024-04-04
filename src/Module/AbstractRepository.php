<?php

namespace Vector\Module;

use Vector\Module\SqlClient;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractRepository
{

    protected static string $class;
    protected static string $tablename;
    protected static mixed $instance = null;
    protected PDO $sql;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct()
    {
        $this->sql = SqlClient::getInstance()
            ->getClient();
    }

    /**
     * @package Vector
     * Vector\Module\AbstractRepository::getInstance()
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Repository\AssetRepository->getBy()
     * @param string $field
     * @param mixed $value
     * @param int $type
     * @return ?Asset
     */
    public function getBy(string $field, mixed $value, int $type): ?static
    {
        $tablename = static::$tablename;
        $query = "SELECT * FROM `{$tablename}` WHERE `{$field}` = :value LIMIT 1";
        $q = $this->sql->prepare($query);

        $q->bindParam('value', $value, $type);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return new static::$class($results);
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\Repository\AssetRepository->getList()
     * @param array $params
     * @return ?array
     */
    public function getList(array $params): ?array
    {
        $params = array_replace([
            'condition' => '1 = 1',
            'orderKey' => 'ID',
            'order' => 'ASC',
            'limit' => 32,
            'offset' => 0
        ], $params);

        $tablename = static::$tablename;
        $query = "SELECT * FROM `{$tablename}` WHERE {$params['condition']} 
            ORDER BY {$params['orderKey']} {$params['order']} 
            LIMIT :limit OFFSET :offset";

        $q = $this->sql->prepare($query);

        $q->bindParam('limit', $params['limit'], PDO::PARAM_INT);
        $q->bindParam('offset', $params['offset'], PDO::PARAM_INT);
        $q->execute();

        if (false !== ($results = $q->fetchAll(PDO::FETCH_ASSOC))) {
            return array_map(fn($el) => new static::$class($el), $results);
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\Repository\AssetRepository->getTotalCount()
     * @return int 
     */
    public function getTotalCount(): int
    {
        $tablename = static::$tablename;
        $query = "SELECT COUNT(ID) AS `total` FROM `{$tablename}`";
        $q = $this->sql->prepare($query);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return $results['total'];
        }

        return 0;
    }

}