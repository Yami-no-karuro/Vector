<?php

namespace Vector\Module;

use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Settings
{

    /**
     * @package Vector
     * Vector\Module\Settings::get()
     * @param string $key
     * @return ?string
     */
    public static function get(string $key): ?string
    {
        $sql = SqlClient::getInstance()
            ->getClient();

        $query = "SELECT `value` FROM `settings` WHERE `key` = :key";
        $q = $sql->prepare($query);

        $q->bindParam('key', $key, PDO::PARAM_STR);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return $results['value'];
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Settings::set()
     * @param string $key
     * @param string $value
     * @return void
     */
    public static function set(string $key, string $value): void
    {
        $sql = SqlClient::getInstance()
            ->getClient();

        $query = "INSERT INTO `settings` (`key`, `value`) VALUES (:key, :value) ON DUPLICATE KEY UPDATE `value` = :value";
        $q = $sql->prepare($query);

        $q->bindParam('key', $key, PDO::PARAM_STR);
        $q->bindParam('value', $value, PDO::PARAM_STR);
        $q->execute();
    }

    /**
     * @package Vector
     * Vector\Module\Settings::delete()
     * @param string $key
     * @return void
     */
    public static function delete(string $key): void
    {
        $sql = SqlClient::getInstance()
            ->getClient();

        $query = "DELETE FROM `settings` WHERE `key` = :key";
        $q = $sql->prepare($query);

        $q->bindParam('key', $key, PDO::PARAM_STR);
        $q->execute();
    }

}
