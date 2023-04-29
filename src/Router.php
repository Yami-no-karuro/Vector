<?php

namespace Vector;

use Symfony\Component\HttpFoundation\Request;
use Vector\Module\SqlConnection;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Router {

    private static mixed $instance = null;
    protected Request $request;

    /**
     * @package Vector
     * @param Request $request
     * __construct()
     */
    private function __construct(Request $request) 
    {
        $this->request = $request;
    }
   
    /**
     * @package Vector
     * Vector\Router::getInstance()
     * @param Request $request
     * @return Router
     */
    public static function getInstance(Request $request): Router 
    {
        if (self::$instance == null) { self::$instance = new Router($request); }
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
            if ($len > 0 && $scriptName !== '/') { 
                $path = substr($path, $len); 
            }
        }
        if (!in_array($this->request->getMethod(), (array) $httpMethods)) { return; }
        $matches = null;
        $regex = '/' . str_replace('/', '\/', $route) . '/';
        if (!preg_match_all($regex, $path, $matches)) { return; }
        if (is_array($callback)) {
            if (true === DATABASE_ROUTES) {
                $sql = SqlConnection::getInstance();
                $stored = $sql->getResults("SELECT `ID` FROM `routes` WHERE `path` = ? LIMIT 1", [
                    ['type' => 's', 'value' => $path]
                ]);
                if (!$stored['success'] OR empty($stored['data'])) {
                    $sql->exec("INSERT INTO `routes` 
                        (`ID`, `path`, `controller`)
                        VALUES (NULL, ?, ?)", [
                            ['type' => 's', 'value' => $path],
                            ['type' => 's', 'value' => get_class($callback[0])]
                    ]);
                }
            } else {
                $cacheFile = __DIR__ . '/var/cache/router/' . md5($path);
                if (!file_exists($cacheFile)) {
                    $cacheData = [ 'path' => $path, 'controller' => get_class($callback[0]) ];
                    @file_put_contents($cacheFile, serialize($cacheData), LOCK_EX);
                }
            }
        }
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
