<?php

namespace Vector\Module\Security;

use Vector\Module\Settings;
use Vector\Module\SqlClient;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class WebToken
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
     * Vector\Module\Security\WebToken->generate()
     * @param array $payload
     * @param Request $request
     * @param bool $ignoreRequestInfo
     * @return ?string
     */
    public function generate(array $payload, Request $request, bool $ignoreRequestInfo = false): ?string
    {
        if (null !== ($secret = Settings::get('jwt_secret'))) {
            $headers = $this->generateHeaders();
            if (false === $ignoreRequestInfo) {
                $payload['ipAddress'] = $request->getClientIp();
                $payload['userAgent'] = $request->headers->get('User-Agent', 'unknown');
            }
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
     * Vector\Module\Security\WebToken->generateHeaders()
     * @return string
     */
    protected function generateHeaders(): string
    {
        $headers = json_encode(['type' => 'WebToken', 'algo' => 'HS256'
        ]);
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($headers)
        );
    }

    /**
     * @package Vector
     * Vector\Module\Security\WebToken->generatePayload()
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
