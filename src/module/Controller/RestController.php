<?php

namespace Vector\Module\Controller;

use Vector\Module\Controller\AbstractController;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class RestController extends AbstractController
{
    /**
     * @package Vector
     * __construct()
     * @param bool $direct
     */
    public function __construct(bool $direct = false)
    {
        parent::__construct($direct);
    }

}
