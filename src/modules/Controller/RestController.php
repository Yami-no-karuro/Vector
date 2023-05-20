<?php

namespace Vector\Module;

use Vector\Module\AbstractController;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class RestController extends AbstractController {

    protected Request $request;

    /**
     * @package Vector
     * @param Request $request
     * @param string $path
     * @param bool $direct
     * __construct()
     */
    public function __construct(bool $direct = false) 
    {
        parent::__construct($direct);
    }

}