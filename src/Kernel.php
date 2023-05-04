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
    protected string $path;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {

        /** @var Request $request */
        $this->request = Request::createFromGlobals();
        
        /** @var string $path */
        $this->path = parse_url($this->request->getRequestUri())['path'];
        $scriptName = dirname(dirname($this->request->getScriptName()));
        $scriptName = str_replace('\\', '/', $scriptName);
        $len = strlen($scriptName);
        if ($len > 0 && $scriptName !== '/') {
            $this->path = substr($this->path, $len);
        }

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
        
        /** Boot from cache */
        $this->directBoot();

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
                new $controller($this->request, $this->path, false);
            }
        }

    }

    /**
     * @package Vector
     * Vector\Bootstrap->directBoot()
     * @return void
     */
    protected function directBoot() 
    {

        /** 
         * @var array|null $cacheData
         * Try to load route data from cache
         */
        if (true === DATABASE_ROUTES) {
            $sql = SqlConnection::getInstance();
            $cache = $sql->getResults("SELECT `path`, `regex`, `methods`, `controller`, `callback` 
                FROM `routes` WHERE `path` = ? LIMIT 1", [
                    ['type' => 's', 'value' => $this->path]
            ]);
            if ($cache['success'] AND !empty($cache['data'])) { 
                $cacheData = $cache['data']; 
            } else { return; }
        } else {
            $cacheFile = __DIR__ . '/var/cache/router/' . md5($this->path);
            if (file_exists($cacheFile)) { 
                $cacheData = unserialize(@file_get_contents($cacheFile)); 
            } else { return; }
        }
        
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
        if (!preg_match_all($cacheData['regex'], $this->path, $matches)) { return; }
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
        $controller = new $cacheData['controller']($this->request, $this->path, true);
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
        $paramFilepath = __DIR__ . '/globals/params.json';
        if (file_exists($paramFilepath)) {
            $content = json_decode(@file_get_contents($paramFilepath));
            $params = $content;
        }
    }
    
}