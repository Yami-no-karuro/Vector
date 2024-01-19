<?php

namespace Vector\Repository;

use Vector\Module\SqlClient;
use Vector\DataObject\Asset;

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
     * @param int $id
     * @return ?Asset
     */
    public function getById(int $id): ?Asset
    {
        $result = $this->client->getResults("SELECT * FROM `assets` 
            WHERE `ID` = ? LIMIT 1", [
                ['type' => 'd', 'value' => $id]
        ]);
        return ($result['success'] && !empty($result['data'])) ? 
            new Asset($result['data']) : null;
    }

    /**
     * @package Vector
     * Vector\Repository\AssetRepository->getByPath()
     * @param string $path
     * @return ?Asset
     */
    public function getByPath(string $path): ?Asset
    {
        $result = $this->client->getResults("SELECT * FROM `assets` 
            WHERE `path` = ? LIMIT 1", [
                ['type' => 's', 'value' => $path]
        ]);
        return ($result['success'] && !empty($result['data'])) ? 
            new Asset($result['data']) : null;
    }

    /**
     * @package Vector
     * Vector\Repository\AssetRepository->getList()
     * @param array $params
     * @return ?array<Asset>
     */
    public function getList(array $params): ?array
    {
        $result = $this->client->getResults("SELECT * FROM `assets` LIMIT ? OFFSET ?", [
            ['type' => 'd', 'value' => array_key_exists('limit', $params) ? 
                $params['limit'] : 32],
            ['type' => 'd', 'value' => array_key_exists('offset', $params) ? 
                $params['offset'] : 0]
        ]);
        if ($result['success'] && !empty($result['data'])) {
            $data = isset($result['data'][0]) ? $result['data'] : [$result['data']];
            return array_map(fn($el) => new Asset($el), $data);
        }
        return null;
    }

}