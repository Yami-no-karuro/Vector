<?php

namespace Vector\Module\Security;

use Vector\Module\Settings;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class WebToken
{

    /**
     * @package Vector
     * Vector\Module\Security\WebToken::generate()
     * @param array $payload
     * @param Request $request
     * @param bool $ignoreRequestInfo
     * @return ?string
     */
    public static function generate(array $payload, Request &$request, bool $ignoreRequestInfo = false): ?string
    {
        if (null !== ($secret = Settings::get('web_token_secret'))) {
            $headers = self::generateHeaders();
            if (false === $ignoreRequestInfo) {
                $payload['ip_address'] = $request->getClientIp();
                $payload['user_agent'] = $request->headers->get('User-Agent', 'unknown');
            }
            $payload = self::generatePayload($payload);
            $signature = hash_hmac('sha256', $headers . '.' . $payload, $secret, true);
            $encodedSignature = str_replace(
                ['+', '/', '='],
                ['-', '_', ''],
                base64_encode($signature)
            );
            return $headers . '.' . $payload . '.' . $encodedSignature;
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Security\WebToken::isValid()
     * @param string $token
     * @param Request $request
     * @param bool $ignoreRequestInfo
     * @return bool
     */
    public static function isValid(string $token, Request &$request, bool $ignoreRequestInfo = false): bool
    {

        if (null === ($parts = self::getTokenParts($token))) {
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

        if (false === $ignoreRequestInfo) {
            if (!array_key_exists('ip_address', $decodedPayload) || 
                $decodedPayload['ip_address'] !== $request->getClientIp()) {
                    return false;
            }
            if (!array_key_exists('user_agent', $decodedPayload) || 
                $decodedPayload['user_agent'] !== $request->headers->get('User-Agent')) {
                    return false;
            }
        }

        if (null !== ($secret = Settings::get('web_token_secret'))) {
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
     * Vector\Module\Security\WebToken::getPayload()
     * @param string $token
     * @return ?array
     */
    public static function getPayload(string $token): ?array
    {
        if (null === ($tokenParts = self::getTokenParts($token))) {
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
     * Vector\Module\Security\WebToken::generateHeaders()
     * @return string
     */
    protected static function generateHeaders(): string
    {
        $headers = json_encode(['type' => 'WebToken', 'algo' => 'HS256']);
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($headers)
        );
    }

    /**
     * @package Vector
     * Vector\Module\Security\WebToken::generatePayload()
     * @param array $payload
     * @return string
     */
    protected static function generatePayload(array $payload): string
    {
        $payload = json_encode($payload);
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($payload)
        );
    }

    /**
     * @package Vector
     * Vector\Module\Security\WebToken::getTokenParts()
     * @param string $token
     * @return ?array
     */
    protected static function getTokenParts(string $token): ?array
    {
        $parts = explode('.', $token);
        return is_array($parts) ? $parts : null;
    }

}
