<?php
namespace Vector\Objects;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Response {

    public mixed $body;
    public array $headers;

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
     * @param {bool} $die
     * @return void
     */
    public function send(bool $die = false): void {
        foreach ($this->headers as $header) { header($header); }
        echo $this->body;
        if ($die) { die(); }
    }

}