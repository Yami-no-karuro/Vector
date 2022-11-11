<?php
namespace Vector\Engine;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Crypt {

    /**
     * @package Vector
     * Vector\Engine\Crypt::get_decrypted_string()
	 * @param {string} $string
     * @return string
     */
    public static function get_decrypted_string(string $string): string {
        return openssl_decrypt($string, CIPHERING, ENCRYPTION_KEY, false, ENCRYPTION_IV);
    }

    /**
     * @package Vector
     * Vector\Engine\Crypt::get_encrypted_string()
	 * @param {string} $string
     * @return string
     */
    public static function get_encrypted_string(string $string): string {
        return openssl_encrypt($string, CIPHERING, ENCRYPTION_KEY, false, ENCRYPTION_IV);
    }

}