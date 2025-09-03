<?php

namespace Vector\Module\Repository;

use Vector\Module\SqlClient;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractRepository
{
    protected string $class;
    protected string $tablename;
    protected PDO $sql;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->sql = SqlClient::getInstance()
            ->getClient();
    }

    /**
     * @package Vector
     * Vector\Module\Repository\AbstractRepository->getBy()
     * @param string $field
     * @param mixed $value
     * @param int $type
     * @return mixed
     */
    public function getBy(string $field, mixed $value, int $type): mixed
    {
        $query = "SELECT * FROM `{$this->tablename}` WHERE `{$field}` = :value LIMIT 1";
        $q = $this->sql->prepare($query);

        $q->bindParam('value', $value, $type);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC)))
            return new $this->class($results);

        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Repository\AbstractRepository->getList()
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

        $query = "SELECT * FROM `{$this->tablename}` WHERE {$params['condition']} 
            ORDER BY {$params['orderKey']} {$params['order']} 
            LIMIT :limit OFFSET :offset";

        $q = $this->sql->prepare($query);

        $q->bindParam('limit', $params['limit'], PDO::PARAM_INT);
        $q->bindParam('offset', $params['offset'], PDO::PARAM_INT);
        $q->execute();

        if (false !== ($results = $q->fetchAll(PDO::FETCH_ASSOC)))
            return array_map(fn($el) => new $this->class($el), $results);

        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Repository\AbstractRepository->getTotalCount()
     * @return int
     */
    public function getTotalCount(): int
    {
        $query = "SELECT COUNT(ID) AS `total` FROM `{$this->tablename}`";
        $q = $this->sql->prepare($query);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC)))
            return $results['total'];

        return 0;
    }
}
