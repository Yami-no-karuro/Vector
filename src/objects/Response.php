<?php
namespace Vector\Objects;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Response {

    public $body;
    public $headers;

    /**
     * @package Vector
     * __construct();
     */
    public function __construct(mixed $body, array $headers) {
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @package Vector
     * Vector\Objects\Response->send()
     */
    public function send(): void {
        foreach ($this->headers as $header) { header($header); }
        echo $this->body;
    }

}