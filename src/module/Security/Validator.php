<?php

namespace Vector\Module\Security;

use Vector\Module\SqlClient;
use Vector\Module\Settings;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Validator
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
     * Vector\Module\Security\Validator->isValid()
     * @param string $token
     * @param Request $request
     * @param bool $ignoreRequestInfo
     * @return bool
     */
    public function isValid(string $token, Request $request, bool $ignoreRequestInfo = false): bool
    {

        /**
         * @var ?array $parts
         * Token parts are retrived and decoded.
         */
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

        /**
         * @param bool $ignoreRequestInfo
         * Checks requests informations validity.
         */
        if (false === $ignoreRequestInfo) {
            if (!array_key_exists('ip_address', $decodedPayload) || $decodedPayload['ip_address'] !== $request->getClientIp()) {
                return false;
            }
            if (!array_key_exists('user_agent', $decodedPayload) || $decodedPayload['user_agent'] !== $request->headers->get('User-Agent')) {
                return false;
            }
        }

        /**
         * @var ?string $secret
         * The has is validated against the "jwt_secret" option.
         */
        if (null !== ($secret = Settings::get('jwt_secret'))) {
            $calculatedSignature = hash_hmac('sha256', $headers . '.' . $payload, $secret, true);
            $expectedSignature = str_replace(
                ['+', '/', '='],
                ['-', '_', ''],
                base64_encode($calculatedSignature)
            );
            return hash_equals($signature, $expectedSignature);
        }

        return false;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Validator->getPayload()
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
     * Vector\Module\Security\Validator->getTokenParts()
     * @param string $token
     * @return ?array
     */
    protected function getTokenParts(string $token): ?array
    {
        $parts = explode('.', $token);
        return is_array($parts) ? $parts : null;
    }

}
