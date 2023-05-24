<?php

namespace Vector\Module\Transient;

use Vector\Module\Transient\AbstractTransient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class FileSystemTransient extends AbstractTransient
{
    protected string $path;
    protected mixed $data = null;
    protected ?int $time = null;

    /**
     * @package Vector
     * __construct()
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->path = __DIR__ . '/../../../var/cache/transients/' . md5($this->name);
        $this->data = @file_get_contents($this->path, true);
        $this->time = @filemtime($this->path);
    }

    /**
     * @package Vector
     * Vector\Module\Transient\FileSystemTransient->isValid()
     * @param int $seconds
     * @return bool
     */
    public function isValid(int $seconds): bool
    {
        if (!$this->data) {
            return false;
        }
        return (time() - $this->time) > $seconds ? false : true;
    }

    /**
     * @package Vector
     * Vector\Module\Transient\FileSystemTransient->getData()
     * @return mixed
     */
    public function getData(): mixed
    {
        if (!$this->data) {
            return null;
        }
        return unserialize($this->data);
    }

    /**
     * @package Vector
     * Vector\Module\Transient\FileSystemTransient->setData()
     * @param mixed $data
     * @return bool
     */
    public function setData(mixed $data): bool
    {
        return @file_put_contents($this->path, serialize($data), LOCK_EX);
    }

    /**
     * @package Vector
     * Vector\Module\Transient\FileSystemTransient->delete()
     * @return bool
     */
    public function delete(): bool
    {
        return @unlink($this->path);
    }

}
