<?php

namespace Vector\Module\Security;

use Vector\Module\Security\UnauthorizedException;
use Vector\Repository\UserRepository;
use Vector\DataObject\User;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Authentication 
{

    protected const REQUIRED_PAYLOAD_SCHEMA = ['rsid', 'scope', 'time', 'ip_address', 'user_agent'];

    protected UserRepository $repository;
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
        $this->repository = UserRepository::getInstance();
        if (false === ($this->verifyPayload($payload))) {
            throw new UnauthorizedException('Unauthorized');
        }

        $this->user = $this->repository->getById($payload['rsid']);
        $this->scope = $payload['scope'];
        $this->ipAddress = $payload['ip_address'];
        $this->userAgent = $payload['user_agent'];
    }

    /**
     * @package Vector
     * Vector\Module\Security\Authentication->getUserId()
     * @return ?User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Authentication->getScope()
     * @return ?string
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Authentication->getIpAddress()
     * @return ?string
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Authentication->getUserAgent()
     * @return ?string
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Authentication->verifyPayload()
     * @param array $payload
     * @return bool
     */
    protected function verifyPayload(array $payload): bool
    {

        /**
         * @var array $schema
         * The payload is verified and validated.
         */
        $schema = array_keys($payload);
        if (count(array_intersect(self::REQUIRED_PAYLOAD_SCHEMA, $schema)) <= 0) {
            return false;
        }

        return true;
    }

}
