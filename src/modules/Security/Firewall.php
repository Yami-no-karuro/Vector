<?php

namespace Vector\Module\Security;

use Vector\Kernel;
use Vector\Module\Transient\FileSystemTransient;
use Symfony\Component\HttpFoundation\Request;
use Exception;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SecurityException extends Exception
{
}
class Firewall
{
    protected array $patterns;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $transient = new FileSystemTransient('vct-firewall-patterns');
        if ($transient->isValid()) {
            $this->patterns = $transient->getData();
        } else {
            $patternSourcePath = Kernel::getProjectRoot() . '/var/source/firewall_patterns.txt';
            $patternSource = file_get_contents($patternSourcePath);
            $patterns = array_filter(explode("\n", $patternSource), 'trim');
            $transient->setData($patterns);
            $this->patterns = $patterns;
        }
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->verifyRequest()
     * @param Request $request
     * @return void
     */
    public function verifyRequest(Request $request): void
    {
        global $config;
        if (true === $config->firewall->headers) {
            if (null !== ($headers = $request->headers->all())) {
                $this->verifyPayload($headers);
            }
        }
        if (true === $config->firewall->cookies) {
            if (null !== ($cookies = $request->cookies->all())) {
                $this->verifyPayload($cookies);
            }
        }
        if (true === $config->firewall->query) {
            if (null !== ($query = $request->query->all())) {
                $this->verifyPayload($query);
            }
        }
        if (true === $config->firewall->body) {
            if (null !== ($body = $request->request->all())) {
                $this->verifyPayload($body);
            }
        }
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->verifyPayload()
     * @param array $data
     * @return void
     * @throws SecurityException
     */
    protected function verifyPayload(array $data): void
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
