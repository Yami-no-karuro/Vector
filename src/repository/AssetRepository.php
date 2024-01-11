<?php

namespace Vector\Repository;

use Vector\Module\SqlClient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class AssetRepository
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
     * @return ?array
     */
    public function getById(int $id): ?array
    {
        $result = $this->client->getResults("SELECT * FROM `assets` 
            WHERE `ID` = ? LIMIT 1", [
                ['type' => 'd', 'value' => $id]
        ]);
        return ($result['success'] && !empty($result['data'])) ? 
            $result['data'] : null;
    }

    /**
     * @package Vector
     * Vector\Repository\AssetRepository->save()
     * @param array $data
     * @return void
     */
    public function save(array $data): void
    {
        $this->client->exec("INSERT INTO `assets` 
            (`ID`, `path`, `modified_at`, `mimetype`, `size`) VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `path` = ?, `modified_at` = ?, `mimetype` = ?, `size` = ?", [
                ['type' => 's', 'value' => isset($data['ID']) ? $data['ID'] : null],
                ['type' => 's', 'value' => $data['path']],
                ['type' => 's', 'value' => $data['modified_at']],
                ['type' => 's', 'value' => $data['mimetype']],
                ['type' => 's', 'value' => $data['size']],
                ['type' => 's', 'value' => $data['path']],
                ['type' => 's', 'value' => $data['modified_at']],
                ['type' => 's', 'value' => $data['mimetype']],
                ['type' => 's', 'value' => $data['size']]
        ]);
    }

}