<?php

namespace Vector;

use Vector\Module\Transient\FileSystemTransient;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Kernel
{
    protected Request $request;
    protected FileSystemLogger $logger;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        global $request;
        $request = Request::createFromGlobals();
        $this->request = $request;
        $this->logger = new FileSystemLogger('core');
    }

    /**
     * @package Vector
     * Vector\Kernel->boot()
     * @return void
     */
    public function boot(): void
    {
        $this->loadConfig();
        $this->registerShutdownFunctions();
        $this->directBoot();
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
        if (!$transient->isValid(HOUR_IN_SECONDS)) {
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
        if ($transient->isValid(HOUR_IN_SECONDS)) {
            $data = $transient->getData();
        } else {
            $path = __DIR__ . '/../config/config.json';
            $data = json_decode(@file_get_contents($path));
            $transient->setData($data);
        }
        $config = $data;

    }

    /**
     * @package Vector
     * Vector\Kernel->errorShutdown()
     * @return void
     */
    protected function registerShutdownFunctions(): void
    {

        /** @var object $config */
        global $config;

        /**
         * @param int $errno
         * @param string $errstr
         * @param string $errfile
         * @param int $errline
         * Error handler
         */
        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($config) {
            if (true === $config->debug) {
                $errorMessage = 'Error: "' . $errstr . '" in "' . $errfile . '" at line "' . $errline . '"';
                $this->logger->write($errorMessage);
            }
        });

        /**
         * @param Exception $exception
         * Exception handler
         */
        set_exception_handler(function ($exception) use ($config) {
            if (true === $config->debug) {
                $exceptionMessage = 'Exception: "' . $exception->getMessage() . '" in "' . $exception->getFile() . '" at line "' . $exception->getLine() . '"';
                $this->logger->write($exceptionMessage);
            }
        });

        /**
         * @var Response $response
         * @var string $lastError
         * Fatal error handler
         */
        register_shutdown_function(function () use ($config) {
            $lastError = error_get_last();
            if ($lastError !== null && in_array($lastError['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                if (true === $config->debug) {
                    $errorMessage = 'Fatal error: "' . $lastError['message'] . '" in "' . $lastError['file'] . '" at line "' . $lastError['line'] . '"';
                    $this->logger->write($errorMessage);
                    $response = new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
                    $response->prepare($this->request);
                    $response->send();
                }
            }
        });

    }

}
