<?php

namespace Vector\Module\Security;

use Vector\Module\Settings;
use Vector\Module\SqlClient;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class JWT
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
     * Vector\Module\Security\JWT->generate()
     * @param array $payload
     * @param Request $request
     * @return ?string
     */
    public function generate(array $payload, Request $request): ?string
    {
        if (null !== ($secret = Settings::get('jwt_secret'))) {
            $headers = $this->generateHeaders();
            $payload['ipAddress'] = $request->getClientIp();
            $payload['userAgent'] = $request->headers->get('User-Agent');
            $payload = $this->generatePayload($payload);
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
     * Vector\Module\Security\JWT->generateHeaders()
     * @return string
     */
    protected function generateHeaders(): string
    {
        $headers = json_encode([
            'type' => 'JWT',
            'algo' => 'HS256'
        ]);
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($headers)
        );
    }

    /**
     * @package Vector
     * Vector\Module\Security\JWT->generatePayload()
     * @param array $payload
     * @return string
     */
    protected function generatePayload(array $payload): string
    {
        $payload = json_encode($payload);
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($payload)
        );
    }

}