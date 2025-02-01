<?php

namespace Vector\Module\Security;

use Vector\Module\Security\Auth;
use Vector\Module\Security\WebToken;
use Vector\Module\Security\SecurityException;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
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
        $path = getProjectRoot() . 'var/source/firewall/patterns.json';
        $data = json_decode(file_get_contents($path));
        $this->patterns = $data->patterns;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->getPatterns()
     * @return array
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->setPatterns()
     * @return void
     */
    protected function setPatterns(array $patterns): void
    {
        $this->patterns = $patterns;
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->verifyRequest()
     * @param Request $request
     * @return void
     * @throws SecurityException
     */
    public function verifyRequest(Request &$request): void
    {
        global $config;

        if (null !== ($routes = $config->security->authenticated_routes))
            $this->verifyRouteAccess($routes, $request);

        if (true === $config->security->firewall->headers)
            if (null !== ($headers = $request->headers->all()))
                $this->verifyPayload($headers);

        if (true === $config->security->firewall->cookies)
            if (null !== ($cookies = $request->cookies->all()))
                $this->verifyPayload($cookies);

        if (true === $config->security->firewall->query)
            if (null !== ($query = $request->query->all()))
                $this->verifyPayload($query);

        if (true === $config->security->firewall->body)
            if (null !== ($body = $request->request->all()))
                $this->verifyPayload($body);
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->verifyPayload()
     * @param mixed $data
     * @return void
     * @throws SecurityException
     */
    protected function verifyPayload(mixed $data): void
    {
        foreach ($data as $value) {
            if (is_array($value))
                $value = implode(', ', $value);

            foreach ($this->patterns as $pattern) {
                if (preg_match($pattern, $value))
                    throw new SecurityException();
            }
        }
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->verifyRouteAccess()
     * @param array $routes
     * @param Request $request
     * @return void
     * @throws SecurityException
     */
    protected function verifyRouteAccess(array $routes, Request &$request): void
    {
        global $auth;

        foreach ($routes as $route) {
            $regex = '/' . str_replace('/', '\/', $route) . '/';
            if (0 !== preg_match($regex, $request->getPathInfo())) {
                $authToken = null !== ($token = $request->cookies->get('Auth-Token')) ?
                    $token : $request->headers->get('Auth-Token');

                if (null !== $authToken && WebToken::isValid($authToken, $request)) {
                    $payload = WebToken::getPayload($authToken);
                    $auth = new Auth($payload);
                    return;
                }

                throw new SecurityException();
            }
        }
    }
}
