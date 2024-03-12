<?php

namespace Vector;

use Vector\Module\Transient\SqlTransient;
use Vector\Module\EventDispatcher;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Router
{

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
        global $request;

        $matches = null;
        $params = [];
        $regex = '/' . str_replace('/', '\/', $route) . '/';
        if (!in_array($request->getMethod(), (array) $httpMethods)) { return; }
        if (!preg_match_all($regex, $request->getPathInfo(), $matches)) { return; }
        if (!empty($matches)) {
            foreach ($matches as $key => $value) {
                if (!is_numeric($key) && !isset($value[1])) {
                    $params[$key] = $value[0];
                }
            }
        }

        $controller = get_class($callback[0]);
        $method = $callback[1];
        $transient = new SqlTransient('vct-route-{' . $request->getPathInfo() . '}');
        $transient->setData([
            'path' => $request->getPathInfo(),
            'regex' => $regex,
            'methods' => serialize($httpMethods),
            'controller' => $controller,
            'callback' => $method
        ]);

        EventDispatcher::dispatch('KernelListener', 'onCallback', [&$request, $controller, $method, &$params]);
        $response = $callback($request, $params);
        EventDispatcher::dispatch('KernelListener', 'onResponse', [&$request, &$response]);

        $response->prepare($request);
        $response->send();
        die();
    }

}
