<?php
namespace Vector\Objects;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Request {

    /**
     * @package Vector
     * Vector\Objects\Request->get_server_info()
     * @return array
     */
    public function get_server_info(): array {
        $data = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') { continue; }
            $record = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $data[$record] = $value;
        }
        return $data;
    }

    /**
     * @package Vector
     * Vector\Objects\Request->get_headers()
     * @return array
     */
    public function get_headers(): array {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') { continue; }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

}