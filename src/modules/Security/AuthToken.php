<?php

namespace Vector\Module\Security;

use Vector\Module\SqlClient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class AuthToken 
{

    protected array $payload;
    protected SqlClient $sql;

    /**
     * @package Vector
     * __construct()
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->sql = SqlClient::getInstance();
    }

    /**
     * @package Vector
     * Vector\Module\Security\AuthToken->generate()
     * @return ?string
     */
    public function generate(): ?string
    {
        $result = $this->sql->getResults("SELECT `secret` FROM `users` WHERE `ID` = ?", [
            ['type' => 'd', 'value' => $this->payload['userId']]
        ]);
        if (true === $result['success'] AND !empty($data = $result['data'])) {
            $base64UrlHeader = $this->generateHeaders();
            $base64UrlPayload = $this->generatePayload();
            $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $data['secret'], true);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
        }
        return null;
    }

    /**
     * @package Vector
     * Vector\Module\Security\AuthToken->generateHeaders()
     * @return string
     */
    protected function generateHeaders(): string
    {
        $headers = json_encode([
            'type' => 'JWT', 
            'algo' => 'HS256'
        ]);
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($headers));
    }

    /**
     * @package Vector
     * Vector\Module\Security\AuthToken->generatePayload()
     * @return string
     */
    protected function generatePayload(): string
    {
        $payload = json_encode($this->payload);
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    }

}
