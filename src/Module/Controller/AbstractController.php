<?php

namespace Vector\Module\Controller;

use Vector\Module\ApplicationLogger\SqlLogger;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractController
{
    protected Request $request;
    protected SqlLogger $logger;

    /**
     * @package Vector
     * __construct()
     * @param bool $direct
     */
    public function __construct(bool $direct = false)
    {
        global $request;
        $this->request = $request;
        $this->logger = new SqlLogger('controller');

        if (!$direct)
            $this->register();
    }

    /**
     * @package Vector
     * Vector\Module\AbstractController->register()
     * @return void
     */
    abstract protected function register(): void;
}
