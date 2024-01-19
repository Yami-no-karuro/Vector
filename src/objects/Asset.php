<?php

namespace Vector\DataObject;

use Vector\Module\SqlClient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Asset 
{

    protected SqlClient $client;

    protected ?int $ID = null;
    protected string $path;
    protected ?int $modifiedAt = null;
    protected ?string $mimeType = null;
    protected ?int $size = null;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(array $data)
    {
        $this->client = SqlClient::getInstance();
        foreach (array_keys($data) as $key) {
            $this->$key = $data[$key];
        }
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->get()
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return isset($this->$key) ? $this->$key : null;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->save()
     * @return void 
     */
    public function save(): void
    {
        $now = time();
        $this->client->exec("INSERT INTO `assets` 
            (`ID`, `path`, `modifiedAt`, `mimeType`, `size`) VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `path` = ?, `modifiedAt` = ?, `mimeType` = ?, `size` = ?", [
                ['type' => 's', 'value' => $this->get('ID')],
                ['type' => 's', 'value' => $this->get('path')],
                ['type' => 's', 'value' => $now],
                ['type' => 's', 'value' => $this->get('mimeType')],
                ['type' => 's', 'value' => $this->get('size')],
                ['type' => 's', 'value' => $this->get('path')],
                ['type' => 's', 'value' => $now],
                ['type' => 's', 'value' => $this->get('mimeType')],
                ['type' => 's', 'value' => $this->get('size')],
        ]);
    }

}