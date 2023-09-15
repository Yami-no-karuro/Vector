<?php

namespace Vector\Repository;

use Vector\Module\SqlClient;

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
     * @return ?array
     */
    public function getById(int $id): ?array
    {
        $result = $this->client->getResults("SELECT * FROM `users` WHERE `ID` = ? LIMIT 1", [
            ['type' => 'd', 'value' => $id]
        ]);
        return ($result['success'] && !empty($result['data'])) ? $result['data'] : null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->getById()
     * @return ?array
     */
    public function getByEmail(string $email): ?array
    {
        $result = $this->client->getResults("SELECT * FROM `users` WHERE `email` = ? LIMIT 1", [
            ['type' => 's', 'value' => $email]
        ]);
        return ($result['success'] && !empty($result['data'])) ? $result['data'] : null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->insert()
     * @param array $userdata
     * @return void
     */
    public function insert(array $userdata): void
    {
        $this->client->exec("INSERT INTO `users` 
            (`ID`, `email`, `password`, `username`, `firstname`, `lastname`) 
            VALUES (NULL, ?, ?, ?, ?, ?)", [
            ['type' => 's', 'value' => $userdata['email']],
            ['type' => 's', 'value' => hash('sha256', trim($userdata['password']))],
            ['type' => 's', 'value' => trim($userdata['username'])],
            ['type' => 's', 'value' => trim($userdata['firstname'])],
            ['type' => 's', 'value' => trim($userdata['lastname'])]
        ]);
    }
}
