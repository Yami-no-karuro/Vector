<?php

namespace Vector\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OnResponse {

    public function __construct(Request $request, Response $response) 
    {
        // ...
    }

}