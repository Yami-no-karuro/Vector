<?php

namespace Vector\Module\Controller;

use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class AbstractController {

    protected Request $request;

    /**
     * @package Vector
     * __construct()
     * @param bool $direct
     */
    public function __construct(bool $direct = false) 
    {

        /** @var Request $request */
        global $request;
        $this->request = $request;

        /** Routes are not registered when called from Kernel::directBoot() */
        if (!$direct) { $this->register(); }

    }

    
    /**
     * @package Vector
     * Vector\Module\AbstractController->register()
     * @return void
     */
    abstract protected function register(): void;

}