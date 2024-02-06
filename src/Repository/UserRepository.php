<?php

namespace Vector\Repository;

use Vector\Module\SqlClient;
use Vector\DataObject\User;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class UserRepository
{

    private static mixed $instance = null;
    protected SqlClient $client;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct()
    {
        $this->client = SqlClient::getInstance();
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
        $result = $this->client->getResults("SELECT * FROM `users` 
            WHERE `ID` = ? LIMIT 1", [
            ['type' => 'd', 'value' => $id]
        ]);
        return ($result['success'] && !empty($result['data'])) ?
            new User($result['data']) : null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->getByEmail()
     * @return ?User
     */
    public function getByEmail(string $email): ?User
    {
        $result = $this->client->getResults("SELECT * FROM `users` 
            WHERE `email` = ? LIMIT 1", [
            ['type' => 's', 'value' => $email]
        ]);
        return ($result['success'] && !empty($result['data'])) ?
            new User($result['data']) : null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->getList()
     * @param array $params
     * @return ?array<User>
     */
    public function getList(array $params): ?array
    {
        $result = $this->client->getResults("SELECT * FROM `users` LIMIT ? OFFSET ?", [
            ['type' => 'd', 'value' => array_key_exists('limit', $params) ?
                $params['limit'] : 32],
            ['type' => 'd', 'value' => array_key_exists('offset', $params) ?
                $params['offset'] : 0]
        ]);
        if ($result['success'] && !empty($result['data'])) {
            $data = isset($result['data'][0]) ? $result['data'] : [$result['data']];
            return array_map(fn ($el) => new User($el), $data);
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
        $result = $this->client->getResults("SELECT COUNT(ID) AS `total` FROM `users`");
        if ($result['success'] && !empty($result['data'])) {
            return $result['data']['total'];
        }
        return 0;
    }
}
