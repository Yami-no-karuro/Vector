<?php
namespace Vector\Events;

class OnCallback {

    public function __construct(array $args) {
        list($request, $path, $params) = $args;
        // ...
    }

}