<?php

namespace Vector\Module\Security;

use Vector\Module\Security\SecurityException;
use Vector\Repository\UserRepository;
use Vector\DataObject\User;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Auth 
{

    protected User $user;
    protected ?string $scope = null;
    protected ?string $ipAddress = null;
    protected ?string $userAgent = null;

    /**
     * @package Vector
     * __construct()
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $repository = UserRepository::getInstance();
        if (false === ($this->verifyPayload($payload)) || 
            null === ($user = $repository->getById($payload['rsid']))) {
                throw new SecurityException();
        }

        $this->user = $user;
        $this->scope = $payload['scope'];
        $this->ipAddress = $payload['ip_address'];
        $this->userAgent = $payload['user_agent'];
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->getUserId()
     * @return ?User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->getScope()
     * @return ?string
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->getIpAddress()
     * @return ?string
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->getUserAgent()
     * @return ?string
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->verifyPayload()
     * @param array $payload
     * @return bool
     */
    protected function verifyPayload(array $payload): bool
    {
        global $config;

        $schema = array_keys($payload);
        if (count(array_intersect($config->security->authentication_schema, $schema)) <= 0) {
            return false;
        }

        return true;
    }

}
