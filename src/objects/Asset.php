<?php

namespace Vector\DataObject;

use Vector\Kernel;
use Vector\Module\SqlClient;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\S3StorageAdapter;
use Exception;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Asset 
{

    protected FileSystemLogger $logger;
    protected SqlClient $client;
    protected ?S3StorageAdapter $adapter = null;

    /**
     * @var ?int $ID
     * Asset's ID, autogenerate on creation.
     */
    protected ?int $ID = null;

    /**
     * @var string $path
     * Asset's filepath, required.
     */
    protected string $path;

    /**
     * @var ?int $modifiedAt
     * Asset's modification date.
     */
    protected ?int $modifiedAt = null;

    /**
     * @var ?string $mimeType
     * Asset's mimetype.
     */
    protected ?string $mimeType = null;

    /**
     * @var ?int $size
     * Asset's file size.
     */
    protected ?int $size = null;

    /**
     * @var ?string $content
     * Asset's file content.
     */
    protected ?string $content = null;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(array $data)
    {
        global $config;
        if ($config->s3_storage->enabled === true) {
            $this->adapter = S3StorageAdapter::getInstance();
        }
        
        $this->logger = new FileSystemLogger('storage');
        $this->client = SqlClient::getInstance();
        foreach (array_keys($data) as $key) {
            $this->$key = $data[$key];
        }
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getMimeType()
     * @return ?string
     */
    public function getMimeType(): ?string
    {

        /**
         * @var string $filepath
         * @var false|resource $localHandle
         * The media is retrived and the mimeType is returned.
         */
        if (null !== ($filepath = $this->getFullpath())) {
            return false !== ($this->mimeType = mime_content_type($filepath)) ? 
                $this->mimeType : 
                null;
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getSize()
     * @return ?int 
     */
    public function getSize(): ?int
    {

        /**
         * @var string $filepath
         * @var false|resource $localHandle
         * The media is retrived and the filesize is returned.
         */
        if (null !== ($filepath = $this->getFullpath())) {
            return false !== ($this->size = filesize($filepath)) ? 
                $this->size : 
                null;
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getContent()
     * @return ?string 
     */
    public function getContent(): ?string
    {

        /**
         * @var string $filepath
         * @var false|resource $localHandle
         * The media is fopened and the full content is returned.
         */
        if (null !== ($filepath = $this->getFullpath())) {
            return false !== ($localHandle = fopen($filepath, 'r')) ? 
                fread($localHandle, filesize($filepath)) : 
                null;
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getRoute()
     * @return string
     */
    public function getRoute(): string
    {
        return '/storage/' . $this->get('path');
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getStream()
     * @return mixed 
     */
    public function getStream(): mixed 
    {

        /**
         * @var string $filepath
         * @var false|resource $localHandle
         * The media is fopened and the resource handler is returned.
         */
        if (null !== ($filepath = $this->getFullpath())) {
            return false !== ($localHandle = fopen($filepath, 'r')) ? 
                $localHandle : 
                null;
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getFullpath()
     * @return ?string 
     */
    protected function getFullpath(): ?string
    {

        /**
         * @var string $local
         * Looks for the media inside the local storage. 
         */
        $local = Kernel::getProjectRoot() . 'var/storage/' . $this->get('path');
        if (file_exists($local)) {
            return $local;
        }

        /**
         * @var FileSystem $filesystem
         * The S3 filesystem component is initialized.
         * Looks for the media on the remote storage.
         */
        if (null !== $this->adapter) {
            $filesystem = $this->adapter->getFileSystemComponent();
            if ($filesystem->has($this->get('path'))) {

                /**
                 * @var resource $localHandle
                 * @var resource $remoteHandle
                 * The retrived file is copied into the local storage.
                 */
                $localHandle = fopen($local, 'wb');
                $remoteHandle = $filesystem->readStream($this->get('path'));
                stream_copy_to_stream($remoteHandle, $localHandle);
                fclose($localHandle);

                return $local;
            }
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

        /**
         * @var int $time
         * @var array $result
         * The record is saved on the database (upsert).
         */
        $now = time();
        $result = $this->client->exec("INSERT INTO `assets` 
            (`ID`, `path`, `modifiedAt`, `mimeType`, `size`) VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `path` = ?, `modifiedAt` = ?, `mimeType` = ?, `size` = ?", [
                ['type' => 'd', 'value' => $this->get('ID')],
                ['type' => 's', 'value' => $this->get('path')],
                ['type' => 's', 'value' => $now],
                ['type' => 's', 'value' => $this->get('mimeType')],
                ['type' => 's', 'value' => $this->get('size')],
                ['type' => 's', 'value' => $this->get('path')],
                ['type' => 's', 'value' => $now],
                ['type' => 's', 'value' => $this->get('mimeType')],
                ['type' => 's', 'value' => $this->get('size')],
        ]);
        if ($result['success'] && null !== ($insertedId = $result['data']['inserted_id'])) {
            $this->ID = $insertedId;

            /**
            * @var Filesystem $filesystem
            * If the remote storage is enabled the file is directly uploaded to the bucket.
            * Local storage is used by default.
            */
            if (null !== $this->content) {
                try {
                    if (null !== $this->adapter) {
                        $filesystem = $this->adapter->getFileSystemComponent();
                        $filesystem->write('/' . $this->get('path'), $this->get('content'));
                    } else { 
                        file_put_contents(
                            Kernel::getProjectRoot() . 'var/storage/' . $this->get('path'), 
                            $this->get('content')
                        ); 
                    }
                } catch (Exception $e) {
                    $this->logger->write($e);
                }
            }
        }
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->delete()
     * @return void 
     */
    public function delete(): void
    {
        if (null !== $this->get('ID')) {
            $this->client->exec("DELETE FROM `assets` WHERE `ID` = ?", [
                ['type' => 'd', 'value' => $this->get('ID')],
            ]);

            /**
             * @var Filesystem $filesystem
             * The media is deleted from local and remote storage.
             */
            try {
                unlink(Kernel::getProjectRoot() . 'var/storage/' . $this->get('path'));
                if (null !== $this->adapter) {
                    $filesystem = $this->adapter->getFileSystemComponent();
                    $filesystem->delete($this->get('path'));
                } 
            } catch (Exception $e) {
                $this->logger->write($e);
            }
        }
    }

}