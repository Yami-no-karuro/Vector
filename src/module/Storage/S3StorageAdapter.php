<?php

namespace Vector\Module\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\Visibility;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class S3StorageAdapter 
{

    private static mixed $instance = null;
    protected Filesystem $filesystem;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct()
    {

        /**
         * @var object $config
         * @var S3Client $client
         * @var PortableVisibilityConverter $converter
         * @var AwsS3V3Adapter $adapter
         * Filesystem components are initialized.
         */
        global $config;
        $client = new S3Client([
            'region' => $config->assets_storage->region,
            'version' => $config->assets_storage->version,
            'endpoint' => $config->assets_storage->endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => new Credentials(
                $config->assets_storage->access_key, 
                $config->assets_storage->access_secret
            )
        ]);

        $converter = new PortableVisibilityConverter(Visibility::PUBLIC);
        $adapter = new AwsS3V3Adapter($client, $config->assets_storage->bucket, '/', $converter);
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * @package Vector
     * Vector\Module\S3StorageAdapter::getInstance()
     * @return S3StorageAdapter
     */
    public static function getInstance(): S3StorageAdapter
    {
        if (self::$instance == null) {
            self::$instance = new S3StorageAdapter();
        }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Module\S3StorageAdapter->getFileSystemComponent()
     * @return Filesystem
     */
    public function getFileSystemComponent(): Filesystem
    {
        return $this->filesystem;
    }

}