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
        return ($result['success'] && !empty($result['data'])) ? $result['data'] : null;
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
        return ($result['success'] && !empty($result['data'])) ? $result['data'] : null;
    }

    /**
     * @package Vector
     * Vector\Repository\UserRepository->upsert()
     * @param array $userdata
     * @return void
     */
    public function upsert(array $userdata): void
    {

        /**
         * @var string $email
         * @var string $password
         * @var string $username
         * @var string $firstname
         * @var string $lastname
         * User variables are created.
         */
        $email = trim($userdata['email']);
        $password = hash('sha256', trim($userdata['password']));
        $username = trim($userdata['username']);
        $firstname = trim($userdata['firstname']);
        $lastname = trim($userdata['lastname']);
        $this->client->exec("INSERT INTO `users` 
            (`ID`, `email`, `password`, `username`, `firstname`, `lastname`) VALUES (NULL, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `password` = ?, `username` = ?, `firstname` = ?, `lastname` = ?", [
                ['type' => 's', 'value' => $email],
                ['type' => 's', 'value' => $password],
                ['type' => 's', 'value' => $username],
                ['type' => 's', 'value' => $firstname],
                ['type' => 's', 'value' => $lastname],
                ['type' => 's', 'value' => $password],
                ['type' => 's', 'value' => $username],
                ['type' => 's', 'value' => $firstname],
                ['type' => 's', 'value' => $lastname]
        ]);

    }

}
