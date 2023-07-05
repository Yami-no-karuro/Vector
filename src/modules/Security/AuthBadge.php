<?php

namespace Vector\Module\Security;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class AuthBadge
{
    protected int $userId;
    protected string $ipAddress;
    protected string $userAgent;

    /**
     * @package Vector
     * __construct()
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->userId = $payload['userId'];
        $this->ipAddress = $payload['ipAddress'];
        $this->userAgent = $payload['userAgent'];
    }

    /**
     * @package Vector
     * Vector\Module\Security\AuthBadge->getUserId()
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @package Vector
     * Vector\Module\Security\AuthBadge->getIpAddress()
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @package Vector
     * Vector\Module\Security\AuthBadge->getUserAgent()
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

}
