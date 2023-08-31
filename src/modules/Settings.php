<?php

namespace Vector\Module;

use Vector\Module\SqlClient;

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
     * @return string
     */
    public static function get(string $key): ?string
    {
        $sql = SqlClient::getInstance();
        $result = $sql->getResults("SELECT `value` FROM `settings` WHERE `key` = ?", [
            ['type' => 'd', 'value' => $key]
        ]);
        if (true === $result['success'] && !empty($data = $result['data'])) {
            return $data['value'];
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
        $sql = SqlClient::getInstance();
        $sql->exec("INSERT INTO `settings` 
            (`key`, `value`) VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE `value` = ?", [
                ['type' => 's', 'value' => $key],
                ['type' => 's', 'value' => $value],
                ['type' => 's', 'value' => $value]
        ]);
    }

    /**
     * @package Vector
     * Vector\Module\Settings::delete()
     * @param string $key
     * @return void
     */
    public static function delete(string $key): void
    {
        $sql = SqlClient::getInstance();
        $sql->exec("DELETE FROM `settings` WHERE `key` = ?", [
            ['type' => 's', 'value' => $key]
        ]);
    }

}
