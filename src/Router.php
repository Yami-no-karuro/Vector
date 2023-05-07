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
    protected string $path;

    /**
     * @package Vector
     * @param Request $request
     * @param string $path
     * __construct()
     */
    private function __construct(Request $request, string $path) 
    {
        $this->request = $request;
        $this->path = $path;
    }
   
    /**
     * @package Vector
     * Vector\Router::getInstance()
     * @param Request $request
     * @param string $path
     * @return Router
     */
    public static function getInstance(Request $request, string $path): Router 
    {
        if (self::$instance == null) { self::$instance = new Router($request, $path); }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Router->registerRoute()
     * @param array $httpMethods
     * @param string $route
     * @param callable $callback
     * @return void
     */
    public function registerRoute(array $httpMethods, string $route, callable $callback): void 
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
        if (!in_array($this->request->getMethod(), (array) $httpMethods)) { return; }
        if (!preg_match_all($regex, $this->path, $matches)) { return; }
        if (!empty($matches)) {
            foreach ($matches as $key => $value) {
                if (!is_numeric($key) && !isset($value[1])) { $params[$key] = $value[0]; } 
            }
        }

        /** Cache route data for the next request */
        $this->cacheRouteData($callback, $this->path, $regex, $httpMethods);

        /**
         * @var Vector\Controller $controller
         * @var callable $callback
         * Execute controller callback, send the response and die
         */
        $response = $callback($this->request, $params);
        $response->prepare($this->request);
        $response->send();
        die();

    }

    /**
     * @package Vector
     * Vector\Router->cacheRouteData()
     * @param callable $callback
     * @param string $path
     * @param string $regex
     * @param array $httpMethods
     * @return void
     */
    protected function cacheRouteData(callable $callback, string $path, string $regex, array $httpMethods): void {
        if (!is_array($callback)) { return; }
        if (true === DATABASE_ROUTES) {
            
            /** 
             * @var SqlConnection $sql
             * Store route data on the database 
             */
            $sql = SqlConnection::getInstance();
            $stored = $sql->getResults("SELECT `ID` FROM `routes` WHERE `path` = ? LIMIT 1", [
                ['type' => 's', 'value' => $path]
            ]);
            if (!$stored['success'] OR empty($stored['data'])) {
                $sql->exec("INSERT INTO `routes`
                    (`ID`, `path`, `regex`, `methods`, `controller`, `callback`)
                    VALUES (NULL, ?, ?, ?, ?, ?)", [
                        ['type' => 's', 'value' => $path],
                        ['type' => 's', 'value' => $regex],
                        ['type' => 's', 'value' => serialize($httpMethods)],
                        ['type' => 's', 'value' => get_class($callback[0])],
                        ['type' => 's', 'value' => $callback[1]]
                ]);
            }

        } else {

            /**
             * @var string $cacheFile
             * Store route data on the filesystem
             */
            $cacheFile = __DIR__ . '/var/cache/router/' . md5($path);
            if (!file_exists($cacheFile)) {
                $cacheData = [ 
                    'path' => $path,
                    'regex' => $regex,
                    'methods' => $httpMethods, 
                    'controller' => get_class($callback[0]),
                    'callback' => $callback[1]
                ];
                @file_put_contents($cacheFile, serialize($cacheData), LOCK_EX);
            }

        }
    }

}
