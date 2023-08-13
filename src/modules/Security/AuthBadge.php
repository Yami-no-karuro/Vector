<?php

namespace Vector\Module\Security;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class AuthBadge
{
    protected array $payload;

    /**
     * @package Vector
     * __construct()
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * @package Vector
     * Vector\Module\Security\AuthBadge->getUserId()
     * @return ?int
     */
    public function getUserId(): ?int
    {
        return array_key_exists('userId', $this->payload) ? $this->payload['userId'] : null;
    }

    /**
     * @package Vector
     * Vector\Module\Security\AuthBadge->getIpAddress()
     * @return ?string
     */
    public function getIpAddress(): ?string
    {
        return array_key_exists('ipAddress', $this->payload) ? $this->payload['ipAddress'] : null;
    }

    /**
     * @package Vector
     * Vector\Module\Security\AuthBadge->getUserAgent()
     * @return ?string
     */
    public function getUserAgent(): ?string
    {
        return array_key_exists('userAgent', $this->payload) ? $this->payload['userAgent'] : null;
    }

}
