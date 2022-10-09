<?php
namespace Vector\Functions;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    die(); 
}

class Crypt {

    /**
     * @package Vector
     * Vector\Functions\Crypt::get_decrypted_string()
	 * @param {string} $string
     */
    public static function get_decrypted_string(string $string): string {
        return openssl_decrypt($string, CIPHERING, ENCRYPTION_KEY, false, ENCRYPTION_IV);
    }

    /**
     * @package Vector
     * Vector\Functions\Crypt::get_encrypted_string()
	 * @param {string} $string
     */
    public static function get_encrypted_string(string $string): string {
        return openssl_encrypt($string, CIPHERING, ENCRYPTION_KEY, false, ENCRYPTION_IV);
    }

}