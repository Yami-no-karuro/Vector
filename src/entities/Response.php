<?php
namespace Vector\Entities;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Response {

    /**
     * @package Vector
     * __construct();
     */
    public function __construct(
        public mixed $body, 
        public array $headers
    ) {}

    /**
     * @package Vector
     * Vector\Entities\Response->send()
     * @param {bool} $die
     * @return void
     */
    public function send(bool $die = false): void {
        foreach ($this->headers as $header) { header($header); }
        echo $this->body;
        if ($die) { die(); }
    }

}