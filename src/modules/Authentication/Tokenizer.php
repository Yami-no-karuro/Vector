<?php

namespace Vector\Module\Authentication;

use Vector\Kernel;
use Exception;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Tokenizer
{
    /**
     * @package Vector
     * Vector\Module\OpenSSL\TokenManager::generateToken()
     * @param int $refId
     * @return string
     */
    public static function generateToken(int $refId): string
    {
        $signature = self::sign($refId);
        $encryptionResult = self::encrypt($refId);
        $reversibleData = $encryptionResult['encryptedData'];
        $encryptedKey = $encryptionResult['encryptedKey'];
        $iv = $encryptionResult['iv'];
        $token = $reversibleData . $signature . $encryptedKey . $iv;
        return $token;
    }

    /**
     * @package Vector
     * Vector\Module\OpenSSL\TokenManager::verifyToken()
     * @param string $token
     * @return int|false
     */
    public static function verifyToken(string $token): int|false
    {
        $reversibleData = substr($token, 0, 24);
        $signature = substr($token, 24, 344);
        $encryptedKey = substr($token, 368, 344);
        $iv = substr($token, 712);
        $refId = self::decrypt($reversibleData, $encryptedKey, $iv);
        if (true === (self::verifySignature($refId, $signature))) {
            return $refId;
        }
        return false;
    }

    /**
     * @package Vector
     * Vector\Module\OpenSSL\TokenManager::decrypt()
     * @param string $encryptedData
     * @param string $encryptedKey
     * @param string $iv
     * @return int|false
     */
    protected static function decrypt(string $encryptedData, string $encryptedKey, string $iv): int
    {
        global $config;
        $encryptedData = base64_decode($encryptedData);
        $encryptedKey = base64_decode($encryptedKey);
        $iv = base64_decode($iv);
        $privateKeyString = file_get_contents(Kernel::getProjectRoot() . 'var/keys/private.key');
        $privateKey = openssl_pkey_get_private($privateKeyString, $config->openssl->passphrase);
        if ($privateKey === false) {
            $error = openssl_error_string();
            throw new Exception('Error occurred reading private key: ' . $error);
        }
        if (false === (openssl_open($encryptedData, $refId, $encryptedKey, $privateKey, $config->openssl->encryption_cypher, $iv))) {
            throw new Exception('Error occurred opening data');
        }
        return (int) $refId;
    }

    /**
     * @package Vector
     * Vector\Module\OpenSSL\TokenManager::encrypt()
     * @param int $refId
     * @return array
     */
    protected static function encrypt(int $refId): array
    {
        global $config;
        $ivlen = openssl_cipher_iv_length($config->openssl->encryption_cypher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $publicKeyString = file_get_contents(Kernel::getProjectRoot() . 'var/keys/public.key');
        $publicKey = openssl_pkey_get_public($publicKeyString);
        if ($publicKey === false) {
            $error = openssl_error_string();
            throw new Exception('Error occurred reading public key: ' . $error);
        }
        if (false === (openssl_seal($refId, $encryptedData, $envelopedKeys, [$publicKey], $config->openssl->encryption_cypher, $iv))) {
            throw new Exception('Error occurred sealing data');
        }
        return [
            'encryptedData' => base64_encode($encryptedData),
            'encryptedKey' => base64_encode($envelopedKeys[0]),
            'iv' => base64_encode($iv)
        ];
    }

    /**
     * @package Vector
     * Vector\Module\OpenSSL\TokenManager::sign()
     * @param string $data
     * @return string
     */
    protected static function sign(string $data): string
    {
        global $config;
        $privateKeyString = file_get_contents(Kernel::getProjectRoot() . 'var/keys/private.key');
        $privateKey = openssl_pkey_get_private($privateKeyString, $config->openssl->passphrase);
        if ($privateKey === false) {
            $error = openssl_error_string();
            throw new Exception('Error occurred reading private key: ' . $error);
        }
        if (false === (openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256))) {
            throw new Exception('Error occurred signing data');
        }
        return base64_encode($signature);
    }

    /**
     * @package Vector
     * Vector\Module\OpenSSL\TokenManager::verifySignature()
     * @param string $data
     * @param string $signature
     * @return bool
     */
    protected static function verifySignature(string $data, string $signature): bool
    {
        $publicKeyString = file_get_contents(Kernel::getProjectRoot() . 'var/keys/public.key');
        $publicKey = openssl_pkey_get_public($publicKeyString);
        if ($publicKey === false) {
            $error = openssl_error_string();
            throw new Exception('Error occurred reading public key: ' . $error);
        }
        return openssl_verify($data, base64_decode($signature), $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }

}
