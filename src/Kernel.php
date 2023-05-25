<?php

namespace Vector;

use Vector\Module\Transient\FileSystemTransient;
use Symfony\Component\HttpFoundation\Request;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Kernel
{
    protected Request $request;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        global $request;
        $request = Request::createFromGlobals();
        $this->request = $request;
    }

    /**
     * @package Vector
     * Vector\Kernel->boot()
     * @return void
     */
    public function boot(): void
    {
        /** Loads the global configuration */
        $this->loadConfig();

        /** Try to Boot from cache */
        $this->directBoot();

        /** Fallback boot, if cache is not warmed up yep */
        $this->registerBoot();

    }

    /**
     * @package Vector
     * Vector\Kernel->directBoot()
     * @return void
     */
    protected function directBoot(): void
    {

        /**
         * @var FileSystemTransient $transient
         * Try to load route data from cache
         */
        $transient = new FileSystemTransient('vct-route-{' . $this->request->getPathInfo() . '}');
        if (!$transient->isValid(3600)) {
            return;
        }
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
        if (!in_array($this->request->getMethod(), $httpMethods)) {
            return;
        }
        if (!preg_match_all($cacheData['regex'], $this->request->getPathInfo(), $matches)) {
            return;
        }
        if (!empty($matches)) {
            foreach ($matches as $key => $value) {
                if (!is_numeric($key) && !isset($value[1])) {
                    $params[$key] = $value[0];
                }
            }
        }

        /**
         * @var Vector\Controller $controller
         * @var callable $callback
         * Execute controller callback, send the response and die
         */
        $controller = new $cacheData['controller'](true);
        $response = call_user_func_array([$controller, $cacheData['callback']], [$this->request, $params]);
        $response->prepare($this->request);
        $response->send();
        die();

    }

    /**
     * @package Vector
     * Vector\Kernel->registerBoot()
     * @return void
     */
    protected function registerBoot(): void
    {

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
                require_once($file->getPathname());
                $controller = 'Vector\\Controller\\' . basename($fname, '.php');
                new $controller();
            }
        }

    }

    /**
     * @package Vector
     * Vector\Kernel->loadConfig()
     * @return void
     */
    protected function loadConfig(): void
    {

        /**
         * @var FileSystemTransient $transient
         * @var object $config
         */
        global $config;
        $transient = new FileSystemTransient('vct-config');
        if ($transient->isValid(3600)) {
            $data = $transient->getData();
        } else {
            $path = __DIR__ . '/../config/config.json';
            $data = json_decode(@file_get_contents($path));
            $transient->setData($data);
        }
        $config = $data;

    }

}
