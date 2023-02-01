<?php
namespace Vector\Events;

class OnRequest {

    public function __construct(array $args) {
        list($request) = $args;
        // ...
    }

}