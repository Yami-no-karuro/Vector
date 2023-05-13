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

        /** 
         * @var FileSystemTransient|SqlTransient $transient
         * Cache route data
         */
        if (true === DATABASE_TRANSIENTS) {
            $transient = new SqlTransient('route{' . $this->path . '}');
        } else { $transient = new FileSystemTransient('route{' . $this->path . '}'); }
        $transient->setData([
            'path' => $this->path,
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
        $response = $callback($this->request, $params);
        $response->prepare($this->request);
        $response->send();
        die();

    }

}
