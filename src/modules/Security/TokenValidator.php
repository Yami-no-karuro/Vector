<?php

namespace Vector\Module\Security;

use Vector\Module\SqlClient;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class TokenValidator
{
    protected string $token;
    protected SqlClient $sql;

    /**
     * @package Vector
     * __construct()
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        $this->sql = SqlClient::getInstance();
    }

    /**
     * @package Vector
     * Vector\Module\Security\TokenValidator->isValid()
     * @return bool
     */
    public function isValid(): bool
    {
        if (null === ($tokenParts = $this->getTokenParts())) {
            return false;
        }
        $base64UrlHeader = $tokenParts[0];
        $base64UrlPayload = $tokenParts[1];
        $base64UrlSignature = $tokenParts[2];
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);
        if (false === $this->validatePayload($payload)) {
            return false;
        }
        $result = $this->sql->getResults("SELECT `secret` FROM `users` WHERE `ID` = ? LIMIT 1", [
            ['type' => 'd', 'value' => $payload['userId']]
        ]);
        if (true === $result['success'] and !empty($data = $result['data'])) {
            $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $data['secret'], true);
            $expectedBase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            return hash_equals($base64UrlSignature, $expectedBase64UrlSignature);
        }
        return false;
    }

    /**
     * @package Vector
     * Vector\Module\Security\TokenValidator->getPayload()
     * @return ?array
     */
    public function getPayload(): ?array
    {
        if (null === ($tokenParts = $this->getTokenParts())) {
            return null;
        }
        $base64UrlPayload = $tokenParts[1];
        return json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);
    }

    /**
     * @package Vector
     * Vector\Module\Security\TokenValidator->getTokenParts()
     * @return ?array
     */
    protected function getTokenParts(): ?array
    {
        $tokenParts = explode('.', $this->token);
        if (is_array($tokenParts) and count($tokenParts) === 3) {
            return $tokenParts;
        }
        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Security\TokenValidator->validatePayload()
     * @return bool
     */
    protected function validatePayload(mixed $payload): bool
    {
        global $config;
        global $request;
        if (is_array($payload) and array_keys($payload) === $config->security->token_schema) {
            $ipAddress = $request->getClientIp();
            $userAgent = $request->headers->get('User-Agent');
            if ($payload['ipAddress'] === $ipAddress and $payload['userAgent'] === $userAgent) {
                return true;
            }            
        }
        return false;
    }

}
