<?php

namespace Vector\Module\Security;

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
        return 'Encrypted!';
    }

    /**
     * @package Vector
     * Vector\Module\Crypt::decrypt()
     * @param string $data
     * @return string
     */
    public static function decrypt(string $data): string
    {
        return 'Decrypted!';
    }

}
