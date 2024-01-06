<?php

namespace Vector\Module\Security;

use Exception;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SecurityException extends Exception
{

    protected string $content;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * @package Vector
     * Vector\Module\Security\SecurityException->getContent()
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

}
