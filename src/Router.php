<?php

namespace Vector;

use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Router {

    private static mixed $instance = null;
    protected Request $request;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct() 
    {
        $this->request = Request::createFromGlobals();
    }
   
    /**
     * @package Vector
     * Vector\Router::getInstance()
     * @return Router
     */
    public static function getInstance(): Router 
    {
        if (self::$instance == null) { self::$instance = new Router(); }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Router->registerRoute()
     * @param array $httpMethods
     * @param string $route
     * @param callable $callback
     * @param bool $die = true
     * @return void
     */
    public function registerRoute(array $httpMethods, string $route, callable $callback, bool $die = true): void 
    {
        static $path = null;
        if ($path === null) {
            $path = parse_url($this->request->getRequestUri())['path'];
            $scriptName = dirname(dirname($this->request->getScriptName()));
            $scriptName = str_replace('\\', '/', $scriptName);
            $len = strlen($scriptName);
            if ($len > 0 && $scriptName !== '/') { $path = substr($path, $len); }
        }
        if (!in_array($this->request->getMethod(), (array) $httpMethods)) { return; }
        $matches = null;
        $regex = '/' . str_replace('/', '\/', $route) . '/';
        if (!preg_match_all($regex, $path, $matches)) { return; }
        $params = array();
        if (!empty($matches)) {
            foreach ($matches as $k => $v) { 
                if (!is_numeric($k) && !isset($v[1])) { $params[$k] = $v[0]; } 
            }
        }
        $response = $callback($this->request, $params);
        $response->prepare($this->request);
        $response->send();
        if ($die) { die(); }
    }

}
