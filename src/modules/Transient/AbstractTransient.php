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
        $this->name = $name;
    }

    /**
     * @package Vector
     * Vector\Module\Transient\AbstractTransient->isValid()
     * @param int $seconds
     * @return bool
     */
    abstract public function isValid(int $seconds): bool;

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
     * @return bool
     */
    abstract public function setData(mixed $data): bool;

    /**
     * @package Vector
     * Vector\Module\Transient\AbstractTransient->delete()
     * @return bool
     */
    abstract public function delete(): bool;

}
