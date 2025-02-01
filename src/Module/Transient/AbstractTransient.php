<?php

namespace Vector\Module\Transient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractTransient
{
    protected string $name;

    /**
     * @package Vector
     * __construct()
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = md5($name);
    }

    /**
     * @package Vector
     * Vector\Module\Transient\AbstractTransient->isValid()
     * @return bool
     */
    abstract public function isValid(): bool;

    /**
     * @package Vector
     * Vector\Module\Transient\AbstractTransient->getData()
     * @return mixed
     */
    abstract public function getData(): mixed;

    /**
     * @package Vector
     * Vector\Module\Transient\AbstractTransient->setData()
     * @param mixed $data
     * @param int $ttl
     * @return void
     */
    abstract public function setData(mixed $data, int $ttl): void;

    /**
     * @package Vector
     * Vector\Module\Transient\AbstractTransient->delete()
     * @return void
     */
    abstract public function delete(): void;
}
