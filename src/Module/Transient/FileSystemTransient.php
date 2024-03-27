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
    protected ?array $content = null;

    /**
     * @package Vector
     * __construct()
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->path = getProjectRoot() . 'var/cache/transients/' . $this->name;
        if (file_exists($this->path) && 
            false !== ($data = file_get_contents($this->path, true))) {
                $this->content = unserialize($data);
        }
    }

    /**
     * @package Vector
     * Vector\Module\Transient\FileSystemTransient->isValid()
     * @return bool
     */
    public function isValid(): bool
    {
        if (null !== $this->content &&
            ($this->content['ttl'] === 0 ||
            time() - $this->content['time'] < $this->content['ttl'])) {
                return true;
        }

        return false;
    }

    /**
     * @package Vector
     * Vector\Module\Transient\FileSystemTransient->getData()
     * @return mixed
     */
    public function getData(): mixed
    {
        if (null !== $this->content) {
            return $this->content['data'];
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Transient\FileSystemTransient->setData()
     * @param mixed $data
     * @param int $ttl
     * @return void
     */
    public function setData(mixed $data, int $ttl = 0): void
    {
        $content = [
            'time' => time(),
            'ttl' => $ttl,
            'data' => $data
        ];

        $this->content = $content;
        $serialized = serialize($content);
        file_put_contents($this->path, $serialized, LOCK_EX);
    }

    /**
     * @package Vector
     * Vector\Module\Transient\FileSystemTransient->delete()
     * @return void
     */
    public function delete(): void
    {
        unlink($this->path);
    }

}
