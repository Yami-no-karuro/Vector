<?php

namespace Vector\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class OnResponse {

    public function __construct(Request $request, Response $response) 
    {
        // ...
    }

}