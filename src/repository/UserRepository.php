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
        $result = $this->client->getResults("SELECT * FROM `users` 
            WHERE `ID` = ? LIMIT 1", [
                ['type' => 'd', 'value' => $id]
        ]);
        return ($result['success'] && !empty($result['data'])) ? 
            $result['data'] : null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->getById()
     * @return ?array
     */
    public function getByEmail(string $email): ?array
    {
        $result = $this->client->getResults("SELECT * FROM `users` 
            WHERE `email` = ? LIMIT 1", [
                ['type' => 's', 'value' => $email]
        ]);
        return ($result['success'] && !empty($result['data'])) ? 
            $result['data'] : null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->update()
     * @param array $data
     * @return void
     */
    public function update(array $data): void
    {
        $password = hash('sha256', trim($data['password']));
        $this->client->exec("INSERT INTO `users` 
            (`ID`, `email`, `password`, `username`, `firstname`, `lastname`) VALUES (NULL, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `password` = ?, `username` = ?, `firstname` = ?, `lastname` = ?", [
                ['type' => 's', 'value' => $data['email']],
                ['type' => 's', 'value' => $password],
                ['type' => 's', 'value' => $data['username']],
                ['type' => 's', 'value' => $data['firstname']],
                ['type' => 's', 'value' => $data['lastname']],
                ['type' => 's', 'value' => $password],
                ['type' => 's', 'value' => $data['username']],
                ['type' => 's', 'value' => $data['firstname']],
                ['type' => 's', 'value' => $data['lastname']]
        ]);
    }

}
