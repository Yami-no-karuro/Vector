<?php

namespace Vector\Module\Storage;

use Vector\Kernel;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class MediaStreamer 
{

    /**
     * @package Vector
     * Vector\Module\Storage\MediaStreamer::getStream()
     * @param string $file
     * @return mixed
     */
    public static function getStream(string $filepath): mixed 
    {

        /**
         * @var string $local
         * Looks for the media inside the local storage. 
         */
        $local = Kernel::getProjectRoot() . 'var/storage' . $filepath;
        if (file_exists($local)) {
            return false !== ($localHandle = fopen($local, 'r')) ? 
                [
                    'handle' => $localHandle,
                    'mimeType' => mime_content_type($local),
                    'fileSize' => filesize($local)
                ] : null;
        }

        global $config;
        if ($config->s3_storage->enabled === true) {

            /**
             * @var S3StorageAdapter $adapter
             * @var FileSystem $filesystem
             * The S3 filesystem component is initialized.
             * Looks for the media on the remote storage.
             */
            $adapter = S3StorageAdapter::getInstance();
            $filesystem = $adapter->getFileSystemComponent();
            if ($filesystem->has($filepath)) {

                /**
                 * @var resource $localHandle
                 * @var resource $remoteHandle
                 * The retrived file is copied into the local storage.
                 */
                $localHandle = fopen($local, 'wb');
                $remoteHandle = $filesystem->readStream($filepath);
                stream_copy_to_stream($remoteHandle, $localHandle);
                fclose($localHandle);

                return false !== ($localHandle = fopen($local, 'r')) ? 
                    [
                        'handle' => $localHandle,
                        'mimeType' => mime_content_type($local),
                        'fileSize' => filesize($local)
                    ] : null;
            }
        }

        return null;
    }

}