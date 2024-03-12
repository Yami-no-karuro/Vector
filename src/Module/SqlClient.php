<?php

namespace Vector\Module;

use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SqlClient
{

    protected PDO $client;
    private static mixed $instance = null;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct()
    {
        global $config;
        $this->client = new PDO(
            "mysql:host={$config->database->db_host};dbname={$config->database->db_name}", 
            $config->database->db_user, 
            $config->database->db_password
        );

        $this->client->setAttribute(
            PDO::ATTR_ERRMODE, 
            PDO::ERRMODE_EXCEPTION
        );
    }

    /**
     * @package Vector
     * Vector\Module\SqlClient::getInstance()
     * @return SqlClient
     */
    public static function getInstance(): SqlClient
    {
        if (self::$instance == null) {
            self::$instance = new SqlClient();
        }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Module\SqlClient->getClient()
     * @return PDO 
     */
    public function getClient(): PDO
    {
        return $this->client;
    }

}
