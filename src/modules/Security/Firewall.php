<?php

namespace Vector\Module\Security;

use Vector\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Exception;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SecurityException extends Exception {}
class Firewall
{

    protected array $patterns;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $patternSourcePath = Kernel::getProjectRoot() . '/var/source/firewall_patterns.txt';
        $patternSource = file_get_contents($patternSourcePath);
        $this->patterns = array_filter(explode("\n", $patternSource), 'trim');
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->checkRequest()
     * @param Request $request
     * @return void
     */
    public function checkRequest(Request $request): void
    {
        $headers = $request->headers->all();
        $this->checkData($headers);
        $cookies = $request->cookies->all();
        $this->checkData($cookies);
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->checkData()
     * @param array $data
     * @return void
     * @throws SecurityException
     */
    protected function checkData(array $data): void
    {
        foreach ($data as $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            foreach ($this->patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    throw new SecurityException();
                }
            }
        }
    }

}
