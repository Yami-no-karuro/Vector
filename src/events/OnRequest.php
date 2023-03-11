<?php

namespace Vector\Event;

use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class OnRequest {

    public function __construct(Request $request) 
    {}

}