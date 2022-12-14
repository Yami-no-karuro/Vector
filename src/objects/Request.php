<?php
namespace Vector\Objects;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Request {

    public mixed $request_method;
    public mixed $request_uri;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct() {
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->request_uri = $_SERVER['REQUEST_URI'];
    }

    /**
     * @package Vector
     * Vector\Objects\Request->get_request_headers()
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