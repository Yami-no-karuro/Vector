<?php

namespace Vector\Module\Security;

use Vector\Repository\UserRepository;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class UserAuthBadge
{
    protected array $payload;
    protected UserRepository $repository;

    /**
     * @package Vector
     * __construct()
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->repository = UserRepository::getInstance();
    }

    /**
     * @package Vector
     * Vector\Module\Security\UserAuthBadge->getUserId()
     * @return ?array
     */
    public function getUser(): ?array
    {
        if (array_key_exists('rsid', $this->payload)) {
            return $this->repository->getById($this->payload['rsid']);
        }
        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Security\UserAuthBadge->getIpAddress()
     * @return ?string
     */
    public function getIpAddress(): ?string
    {
        return array_key_exists('ip_address', $this->payload) ? $this->payload['ip_address'] : null;
    }

    /**
     * @package Vector
     * Vector\Module\Security\UserAuthBadge->getUserAgent()
     * @return ?string
     */
    public function getUserAgent(): ?string
    {
        return array_key_exists('user_agent', $this->payload) ? $this->payload['user_agent'] : null;
    }

}
