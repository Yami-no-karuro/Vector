<?php

namespace Vector;

use Vector\Module\SqlConnection;
use Symfony\Component\HttpFoundation\Request;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Kernel {

    protected Request $request;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    /**
     * @package Vector
     * Vector\Bootstrap->boot()
     * @return void
     */
    public function boot(): void
    {
        /** Loads the global $params variable */
        $this->loadGlobals();
        
        /** Try to boot from existing cache */
        $this->tryCacheBoot();

        /**
         * @var RecursiveDirectoryIterator $dir
         * @var RecursiveIteratorIterator $iterator
         * Recursively initialize controllers, parse request trough the Router instance
         */
        $dir = new RecursiveDirectoryIterator(__DIR__ . '/../src/controllers');
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            $fname = $file->getFilename();
            if (preg_match('%\.php$%', $fname)) { 
                require_once ($file->getPathname());
                $controller = 'Vector\\Controller\\' . basename($fname, '.php'); 
                new $controller($this->request); 
            }
        }

    }

    /**
     * @package Vector
     * Vector\Bootstrap->tryCacheBoot()
     * @return void
     */
    protected function tryCacheBoot() 
    {

        /** 
         * @var mixed $path
         * Retrive the correct request path
         */
        $path = parse_url($this->request->getRequestUri())['path'];
        $scriptName = dirname(dirname($this->request->getScriptName()));
        $scriptName = str_replace('\\', '/', $scriptName);
        $len = strlen($scriptName);
        if ($len > 0 && $scriptName !== '/') { 
            $path = substr($path, $len); 
        }

        /** 
         * @var array|null $cacheData
         * Try to load route data from cache
         */
        $cacheData = null;
        if (true === DATABASE_ROUTES) {
            $sql = SqlConnection::getInstance();
            $cache = $sql->getResults("SELECT `path`, `regex`, `methods`, `controller`, `callback` 
                FROM `routes` WHERE `path` = ? LIMIT 1", [
                    ['type' => 's', 'value' => $path]
            ]);
            if ($cache['success'] AND !empty($cache['data'])) { $cacheData = $cache['data']; }
        } else {
            $cacheFile = __DIR__ . '/var/cache/router/' . md5($path);
            if (file_exists($cacheFile)) { $cacheData = unserialize(@file_get_contents($cacheFile)); }
        }
        if (null === $cacheData) { return; }
        
        /** 
         * @var array $httpMethods
         * Retrive allowed http methods for current route, needs to be unserialized when saved on database
         */
        if (true === DATABASE_ROUTES) { 
            $httpMethods = unserialize($cacheData['methods']);
        } else { $httpMethods = $cacheData['methods']; }

        /**
         * @var array|null $matches
         * @var array $params
         * Match request method against route regex and allowed requests methods,
         * retrive matched params if any were passed on the request
         */
        $matches = null;
        $params = [];
        if (!in_array($this->request->getMethod(), $httpMethods)) { return; }
        if (!preg_match_all($cacheData['regex'], $path, $matches)) { return; }
        if (!empty($matches)) {
            foreach ($matches as $key => $value) { 
                if (!is_numeric($key) && !isset($value[1])) { $params[$key] = $value[0]; } 
            }
        }

        /**
         * @var Vector\Controller $controller
         * @var callable $callback
         * Execute controller callback, send the response and die
         */
        $controller = new $cacheData['controller']($this->request, true);
        $response = call_user_func_array([$controller, $cacheData['callback']], [$this->request, $params]);
        $response->prepare($this->request);
        $response->send();
        die();

    }

    /**
     * @package Vector
     * Vector\Bootstrap->loadGlobals()
     * @return void
     */
    protected function loadGlobals(): void 
    {
        global $params;
        $params = [
            'foo' => 'bar',
            'bar' => 'foo'
        ];
    }
    
}