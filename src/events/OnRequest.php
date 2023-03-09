<?php

namespace Vector\Event;

use Symfony\Component\HttpFoundation\Request;

class OnRequest {

    public function __construct(Request $request) 
    {
        // ...
    }

}