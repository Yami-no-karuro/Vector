<?php

namespace Vector\Module\Security;

use Vector\Module\SqlClient;
use Vector\Module\Settings;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class JWTValidator
{
    protected SqlClient $sql;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->sql = SqlClient::getInstance();
    }

    /**
     * @package Vector
     * Vector\Module\Security\JWTValidator->isValid()
     * @param string $token
     * @param Request $request
     * @return bool
     */
    public function isValid(string $token, Request $request): bool
    {
        if (null === ($parts = $this->getTokenParts($token))) {
            return false;
        }
        list($headers, $payload, $signature) = $parts;
        $decodedPayload = json_decode(base64_decode(
            str_replace(
                ['-', '_'],
                ['+', '/'],
                $payload
            )
        ), true);
        if (!array_key_exists('ipAddress', $decodedPayload) ||
            $decodedPayload['ipAddress'] !== $request->getClientIp() ||
            !array_key_exists('userAgent', $decodedPayload) ||
            $decodedPayload['userAgent'] !== $request->headers->get('User-Agent')) {
            return false;
        }
        $secret = Settings::get('jwt_secret');
        $calculatedSignature = hash_hmac('sha256', $headers . '.' . $payload, $secret, true);
        $expectedSignature = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($calculatedSignature)
        );
        return hash_equals($signature, $expectedSignature);
    }

    /**
     * @package Vector
     * Vector\Module\Security\JWTValidator->getPayload()
     * @param string $token
     * @return ?array
     */
    public function getPayload(string $token): ?array
    {
        if (null === ($tokenParts = $this->getTokenParts($token))) {
            return null;
        }
        $base64UrlPayload = $tokenParts[1];
        return json_decode(base64_decode(
            str_replace(
                ['-', '_'],
                ['+', '/'],
                $base64UrlPayload
            )
        ), true);
    }

    /**
     * @package Vector
     * Vector\Module\Security\JWTValidator->getTokenParts()
     * @param string $token
     * @return ?array
     */
    protected function getTokenParts(string $token): ?array
    {
        $parts = explode('.', $token);
        return is_array($parts) ? $parts : null;
    }

}
