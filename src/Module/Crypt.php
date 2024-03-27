<?php

namespace Vector\Module;

use Vector\Module\Settings;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Crypt
{

    /**
     * @package Vector
     * Vector\Module\Crypt::encrypt()
     * @param string $data
     * @return string
     */
    public static function encrypt(string $data): string
    {
        $key = Settings::get('crypt_key');
        $iv = Settings::get('crypt_iv');
        $startSalt = openssl_random_pseudo_bytes(32);
        $endSalt = openssl_random_pseudo_bytes(32);
        $data = $startSalt . $data . $endSalt;

        return openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    }

    /**
     * @package Vector
     * Vector\Module\Crypt::decrypt()
     * @param string $data
     * @return string
     */
    public static function decrypt(string $data): string
    {
        $key = Settings::get('crypt_key');
        $iv = Settings::get('crypt_iv');
        $decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, 0, $iv);

        return substr($decrypted, 32, (strlen($decrypted) - 64));
    }

}
