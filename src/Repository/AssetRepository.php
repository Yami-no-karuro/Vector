<?php

namespace Vector\Repository;

use Vector\Module\SqlClient;
use Vector\DataObject\Asset;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class AssetRepository
{

    private static mixed $instance = null;
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
     * Vector\Repository\AssetRepository::getInstance()
     * @return AssetRepository
     */
    public static function getInstance(): AssetRepository
    {
        if (self::$instance == null) {
            self::$instance = new AssetRepository();
        }

        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Repository\AssetRepository->getById()
     * @param int $id
     * @return ?Asset
     */
    public function getById(int $id): ?Asset
    {
        $query = "SELECT * FROM `assets` WHERE `ID` = :id LIMIT 1";
        $q = $this->sql->prepare($query);

        $q->bindParam('id', $id, PDO::PARAM_INT);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return new Asset($results);
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\Repository\AssetRepository->getBy()
     * @param string $field
     * @param mixed $value
     * @param int $type
     * @return ?Asset
     */
    public function getBy(string $field, mixed $value, int $type): ?Asset
    {
        $query = "SELECT * FROM `assets` WHERE `{$field}` = :value LIMIT 1";
        $q = $this->sql->prepare($query);

        $q->bindParam('value', $value, $type);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return new Asset($results);
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

        $query = "SELECT * FROM `assets` WHERE {$params['condition']} 
            ORDER BY {$params['orderKey']} {$params['order']} 
            LIMIT :limit OFFSET :offset";

        $q = $this->sql->prepare($query);

        $q->bindParam('limit', $params['limit'], PDO::PARAM_INT);
        $q->bindParam('offset', $params['offset'], PDO::PARAM_INT);
        $q->execute();

        if (false !== ($results = $q->fetchAll(PDO::FETCH_ASSOC))) {
            return array_map(fn($el) => new Asset($el), $results);
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
        $query = "SELECT COUNT(ID) AS `total` FROM `assets`";
        $q = $this->sql->prepare($query);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return $results['total'];
        }

        return 0;
    }

}