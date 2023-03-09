<?php

namespace Vector;

use Vector\Module\RateLimiter;
use Vector\Module\RateExceededException;
use Vector\Module\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Router {

    private static mixed $instance = null;
   
    /**
     * @package Vector
     * Vector\Router::get_instance()
     * @return Router
     */
    public static function get_instance(): Router 
    {
        if (self::$instance == null) { self::$instance = new Router(); }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Router->registerRoute()
     * @param {array} $http_methods
     * @param {string} $route
     * @param {callable} $callback
     * @param {int} $rpm = 60
     * @param {bool} $die = true
     * @return void
     */
    public function registerRoute(array $http_methods, string $route, callable $callback, int $rpm = 120, bool $die = true): void 
    {
        $request = Request::createFromGlobals();
        $rateLimiter = new RateLimiter($request->getClientIp());
        try {
            $rateLimiter->limitRequestsInMinutes($rpm, 1);
        } catch (RateExceededException $e) {
            $response = new Response('', Response::HTTP_TOO_MANY_REQUESTS, [
                'Content-Type: text/plain'
            ]);
            $response->prepare($request);
            $response->send();
        }
        $requestEvent = new EventDispatcher('OnRequest');
        $requestEvent->dispatch([&$request]);
        static $path = null;
        if ($path === null) {
            $path = parse_url($request->getRequestUri())['path'];
            $scriptName = dirname(dirname($request->getScriptName()));
            $scriptName = str_replace('\\', '/', $scriptName);
            $len = strlen($scriptName);
            if ($len > 0 && $scriptName !== '/') { $path = substr($path, $len); }
        }
        if (!in_array($request->getMethod(), (array) $http_methods)) { return; }
        $matches = null;
        $regex = '/' . str_replace('/', '\/', $route) . '/';
        if (!preg_match_all($regex, $path, $matches)) { return; }
        $params = array();
        if (!empty($matches)) {
            foreach ($matches as $k => $v) { 
                if (!is_numeric($k) && !isset($v[1])) { 
                    $params[$k] = $v[0]; 
                } 
            }
        }
        $response = $callback($request, $params);
        $responseEvent = new EventDispatcher('OnResponse');
        $responseEvent->dispatch([&$request, &$response]);
        $response->prepare($request);
        $response->send();
        if ($die) { die(); }
    }

}
