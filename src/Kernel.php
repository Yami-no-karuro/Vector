<?php

namespace Vector;

use Vector\Module\Transient\FileSystemTransient;
use Vector\Module\Transient\SqlTransient;
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
                new $controller($this->request, $this->path);
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
         * @var FileSystemTransient|SqlTransient $transient
         * Try to load route data from cache
         */
        if (true === DATABASE_TRANSIENTS) {
            $transient = new SqlTransient('route{' . $this->path . '}');
        } else { $transient = new FileSystemTransient('route{' . $this->path . '}'); }
        if (!$transient->isValid(900)) { return; }
        $cacheData = $transient->getData();
        $httpMethods = unserialize($cacheData['methods']);

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