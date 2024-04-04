<?php

namespace Vector\Module\Security;

use Vector\Module\Security\SecurityException;
use Vector\Repository\UserRepository;
use Vector\DataObject\User;
use PDO;

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
        $repository = new UserRepository();
        if (false === ($this->verifyPayload($payload)) || 
            null === ($user = $repository->getBy('ID', $payload['rsid'], PDO::PARAM_INT))) {
                throw new SecurityException();
        }

        $this->user = $user;
        $this->scope = $payload['scope'];
        $this->ipAddress = $payload['ip_address'];
        $this->userAgent = $payload['user_agent'];
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->getUser()
     * @return ?User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->setUser()
     * @return void
     */
    protected function setUser(?User $user): void
    {
        $this->user = $user;
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
     * Vector\Module\Security\Auth->setScope()
     * @return void
     */
    protected function setScope(?string $scope): void
    {
        $this->scope = $scope;
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
     * Vector\Module\Security\Auth->setIpAddress()
     * @return void
     */
    protected function setIpAddress(?string $address): void
    {
        $this->ipAddress = $address;
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
     * Vector\Module\Security\Auth->setUserAgent()
     * @return void
     */
    protected function setUserAgent(?string $agent): void
    {
        $this->userAgent = $agent;
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
