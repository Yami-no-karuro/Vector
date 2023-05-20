<?php

namespace Vector;

use Vector\Module\Transient\FileSystemTransient;
use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Router {

    /**
     * @package Vector
     * Vector\Router->route()
     * @param array $httpMethods
     * @param string $route
     * @param callable $callback
     * @return void
     */
    public static function route(array $httpMethods, string $route, callable $callback): void
    {

        /** @var Request $request */
        global $request;

        /**
         * @var array|null $matches
         * @var array $params
         * @var string $regex
         * Match request method against route regex and allowed requests methods,
         * retrive matched params if any were passed on the request
         */
        $matches = null;
        $params = [];
        $regex = '/' . str_replace('/', '\/', $route) . '/';
        if (!in_array($request->getMethod(), (array) $httpMethods)) { return; }
        if (!preg_match_all($regex, $request->getPathInfo(), $matches)) { return; }
        if (!empty($matches)) {
            foreach ($matches as $key => $value) {
                if (!is_numeric($key) && !isset($value[1])) { $params[$key] = $value[0]; } 
            }
        }

        /** 
         * @var FileSystemTransient $transient
         * Cache route data
         */
        $transient = new FileSystemTransient('route{' . $request->getPathInfo() . '}');
        $transient->setData([
            'path' => $request->getPathInfo(),
            'regex' => $regex,
            'methods' => serialize($httpMethods),
            'controller' => get_class($callback[0]),
            'callback' => $callback[1]
        ]);

        /**
         * @var Vector\Controller $controller
         * @var callable $callback
         * Execute controller callback, send the response and die
         */
        $response = $callback($request, $params);
        $response->prepare($request);
        $response->send();
        die();

    }

}
