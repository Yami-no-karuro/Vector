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
    protected string $scope;
    protected string $time;
    protected string $ipAddress;
    protected string $userAgent;

    /**
     * @package Vector
     * __construct()
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $repository = new UserRepository();
        if (null === ($user = $repository->getBy('ID', $payload['resource'], PDO::PARAM_INT))) {
            throw new SecurityException();
        }

        $this->setUser($user);
        $this->setScope($payload['scope']);
        $this->setTime($payload['time']);
        $this->setIpAddress($payload['ip_address']);
        $this->setUserAgent($payload['user_agent']);
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
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->setScope()
     * @return void
     */
    protected function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->getTime()
     * @return int
     */
    public function getTime(): int 
    {
        return $this->time;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->setTime()
     * @return void
     */
    protected function setTime(int $time): void
    {
        $this->time = $time;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->getIpAddress()
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->setIpAddress()
     * @return void
     */
    protected function setIpAddress(string $address): void
    {
        $this->ipAddress = $address;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->getUserAgent()
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Auth->setUserAgent()
     * @return void
     */
    protected function setUserAgent(string $agent): void
    {
        $this->userAgent = $agent;
    }

}
