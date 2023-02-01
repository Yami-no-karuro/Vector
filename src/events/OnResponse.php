<?php
namespace Vector\Events;

class OnResponse {

    public function __construct(array $args) {
        list($request, $response) = $args;
        // ...
    }

}