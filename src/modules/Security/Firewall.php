<?php

namespace Vector\Module\Security;

use Vector\Kernel;
use Vector\Module\Transient\FileSystemTransient;
use Vector\Module\Security\TokenValidator;
use Vector\Module\Security\AuthBadge;
use Vector\Module\Security\SecurityException;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Firewall
{
    protected array $firewallPatterns;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $transient = new FileSystemTransient('vct-firewall-patterns');
        if ($transient->isValid()) {
            $this->firewallPatterns = $transient->getData();
        } else {
            $patternSourcePath = Kernel::getProjectRoot() . '/var/source/firewall_patterns.txt';
            $patternSource = file_get_contents($patternSourcePath);
            $patterns = array_filter(explode("\n", $patternSource), 'trim');
            $transient->setData($patterns);
            $this->firewallPatterns = $patterns;
        }
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->verifyRequest()
     * @param Request $request
     * @return void
     * @throws SecurityException
     */
    public function verifyRequest(Request $request): void
    {

        /**
         * @var object $config
         * Retrive the global configuration variable.
         */
        global $config;

        /**
         * @var array $headers
         * Verify request headers if set in global configuration.
         */
        if (true === $config->firewall->headers) {
            if (null !== ($headers = $request->headers->all())) {
                $this->verifyPayload($headers);
            }
        }

        /**
         * @var array $cookies
         * Verify request cookies if set in global configuration.
         */
        if (true === $config->firewall->cookies) {
            if (null !== ($cookies = $request->cookies->all())) {
                $this->verifyPayload($cookies);
            }
        }

        /**
         * @var array $query
         * Verify request query if set in global configuration.
         */
        if (true === $config->firewall->query) {
            if (null !== ($query = $request->query->all())) {
                $this->verifyPayload($query);
            }
        }

        /**
         * @var array $body
         * Verify request body if set in global configuration.
         */
        if (true === $config->firewall->body) {
            if (null !== ($body = $request->request->all())) {
                $this->verifyPayload($body);
            }
        }

        /**
         * @var array $authenticatedRoutes
         * Look if any protected routes were registered, if so current
         * request is verified through $this->verifyRouteAccess().
         */
        if (null !== ($authenticatedRoutes = $config->security->authenticated_routes)) {
            $this->verifyRouteAccess($authenticatedRoutes, $request);
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
            foreach ($this->firewallPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    throw new SecurityException();
                }
            }
        }
    }

    /**
     * @package Vector
     * Vector\Module\Security\Firewall->verifyRouteAccess()
     * @param array $protectedRoutes
     * @param Request $request
     * @return void
     * @throws SecurityException
     */
    protected function verifyRouteAccess(array $protectedRoutes, Request $request): void
    {
        foreach ($protectedRoutes as $route) {
            $regex = '/' . str_replace('/', '\/', $route) . '/';
            if (0 !== preg_match($regex, $request->getPathInfo())) {

                /**
                 * @var ?string $authToken
                 * Look for authToken in request cookies and headers.
                 */
                if (null === ($authToken = $request->cookies->get('Auth-Token'))) {
                    $authToken = $request->headers->get('Auth-Token');
                }
                if (null !== $authToken) {

                    /**
                     * @var TokenValidator $validator
                     * @var AuthBadge $badge
                     * Validate the retrived token on the TokenValidator instance.
                     */
                    global $badge;
                    $validator = new TokenValidator($authToken);
                    if (true === $validator->isValid()) {
                        $payload = $validator->getPayload();
                        $badge = new AuthBadge($payload);
                        return;
                    }

                }

                throw new SecurityException();
            }
        }
    }

}
