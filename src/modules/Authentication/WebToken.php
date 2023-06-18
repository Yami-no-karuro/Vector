<?php

namespace Vector\Module\Authentication;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class WebToken
{

    /**
     * @package Vector
     * Vector\Module\Authentication\WebToken::generateToken()
     * @param array $payload
     * @return string
     */
    public static function generateToken(array $payload): string
    {
        global $config;
        $base64UrlHeader = self::generateHeaders();
        $base64UrlPayload = self::generatePayload($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $config->auth->key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    /**
     * @package Vector
     * Vector\Module\Authentication\WebToken::validateToken()
     * @param string $token
     * @return bool
     */
    public static function validateToken(string $token): bool
    {
        global $config;
        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = explode('.', $token);
        $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $config->auth->key, true);
        $expectedBase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        return hash_equals($base64UrlSignature, $expectedBase64UrlSignature);
    }

    /**
     * @package Vector
     * Vector\Module\Authentication\WebToken::getPayload()
     * @param string $token
     * @return array
     */
    public static function getPayload(string $token): array
    {
        $tokenParts = explode('.', $token);
        $base64UrlPayload = $tokenParts[1];
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload));
        return json_decode($payload, true);
    }

    /**
     * @package Vector
     * Vector\Module\Authentication\WebToken::getHeaders()
     * @param string $token
     * @return array
     */
    public static function getHeaders(string $token): array
    {
        $tokenParts = explode('.', $token);
        $base64UrlHeader = $tokenParts[0];
        $headers = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlHeader));
        return json_decode($headers, true);
    }

    /**
     * @package Vector
     * Vector\Module\Authentication\WebToken::generateHeaders()
     * @return string
     */
    protected static function generateHeaders(): string
    {
        global $config;
        $headers = json_encode(['typ' => 'JWT', 'alg' => $config->auth->algo]);
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($headers));
    }

    /**
     * @package Vector
     * Vector\Module\Authentication\WebToken::generatePayload()
     * @param array $payload
     * @return string
     */
    protected static function generatePayload(array $payload): string
    {
        $payload = json_encode($payload);
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    }

}