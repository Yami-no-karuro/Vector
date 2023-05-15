<?php

namespace Vector;

use Vector\Module\Transient\FileSystemTransient;
use Vector\Module\Transient\SqlTransient;
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
     * @param Request $request
     * @param string $path
     * @return void
     */
    public static function route(array $httpMethods, string $route, callable $callback, Request $request, string $path): void
    {

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
        if (!preg_match_all($regex, $path, $matches)) { return; }
        if (!empty($matches)) {
            foreach ($matches as $key => $value) {
                if (!is_numeric($key) && !isset($value[1])) { $params[$key] = $value[0]; } 
            }
        }

        /** 
         * @var FileSystemTransient|SqlTransient $transient
         * Cache route data
         */
        if (true === DATABASE_TRANSIENTS) {
            $transient = new SqlTransient('route{' . $path . '}');
        } else { $transient = new FileSystemTransient('route{' . $path . '}'); }
        $transient->setData([
            'path' => $path,
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
