<?php

namespace Vector\Repository;

use Vector\Module\SqlClient;
use Vector\DataObject\User;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class UserRepository
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
     * Vector\Repository\UserRepository::getInstance()
     * @return UserRepository
     */
    public static function getInstance(): UserRepository
    {
        if (self::$instance == null) {
            self::$instance = new UserRepository();
        }

        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->getById()
     * @return ?User
     */
    public function getById(int $id): ?User
    {
        $query = "SELECT * FROM `users` WHERE `ID` = :id LIMIT 1";
        $q = $this->sql->prepare($query);

        $q->bindParam('id', $id, PDO::PARAM_INT);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return new User($results);
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->getBy()
     * @param string $field
     * @param mixed $value
     * @param int $type
     * @return ?User
     */
    public function getBy(string $field, mixed $value, int $type): ?User
    {
        $query = "SELECT * FROM `users` WHERE `{$field}` = :value LIMIT 1";
        $q = $this->sql->prepare($query);

        $q->bindParam('value', $value, $type);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return new User($results);
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->getList()
     * @param string $condition
     * @param int $limit
     * @param int $offset 
     * @return ?array
     */
    public function getList(string $condition = '1 = 1', int $limit = 32, int $offset = 0): ?array
    {
        $query = "SELECT * FROM `users` WHERE {$condition} LIMIT :limit OFFSET :offset";
        $q = $this->sql->prepare($query);

        $q->bindParam('limit', $limit, PDO::PARAM_INT);
        $q->bindParam('offset', $offset, PDO::PARAM_INT);
        $q->execute();

        if (false !== ($results = $q->fetchAll(PDO::FETCH_ASSOC))) {
            return array_map(fn($el) => new User($el), $results);
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->getTotalCount()
     * @return int 
     */
    public function getTotalCount(): int
    {
        $query = "SELECT COUNT(ID) AS `total` FROM `users`";
        $q = $this->sql->prepare($query);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return $results['total'];
        }

        return 0;
    }

}
