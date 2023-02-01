<?php
namespace Vector\Objects;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Session {

    /**
     * @package Vector
     * Vector\Objects\Session::get()
     * @param {string} $key
     * @return mixed
     */
    public static function get(string $key): mixed {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return null;
    }

    /**
     * @package Vector
     * Vector\Objects\Session::set()
     * @param {string} $key
     * @param {mixed} $val
     * @return {void}
     */
    public static function set(string $key, mixed $val): void {
        $_SESSION[$key] = $val;
    }

}