<?php
namespace Vector\Objects;

use Carbon\Carbon;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Request {

    public string $document_root;
    public string $script_name;
    public string $script_filename;
    public string $remote_address;
    public string $remote_port;
    public string $request_scheme;
    public string $request_method;
    public string $request_uri;
    public int $request_time;
    public float $request_time_float;

    /**
     * @package Vector
     * __construct();
     */
    public function __construct() {
        $this->document_root = $_SERVER['DOCUMENT_ROOT'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
        $this->script_filename = $_SERVER['SCRIPT_FILENAME'];
        $this->remote_address = $_SERVER['REMOTE_ADDR'];
        $this->remote_port = $_SERVER['REMOTE_PORT'];
        $this->request_scheme = $_SERVER['REQUEST_SCHEME'];
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->request_uri = $_SERVER['REQUEST_URI'];
        $this->request_time_float = $_SERVER['REQUEST_TIME_FLOAT'];
        $this->request_time = $_SERVER['REQUEST_TIME'];
    }

    /**
     * @package Vector
     * Vector\Objects\Request->get_server_info()
     * @return array
     */
    public function get_server_info(): array {
        $data = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'SERVER_') { continue; }
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