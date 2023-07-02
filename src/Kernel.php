<?php

namespace Vector;

use Vector\Module\Security\Firewall;
use Vector\Module\Security\SecurityException;
use Vector\Module\Transient\FileSystemTransient;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\Event\EventDispatcher;
use Vector\Module\ErrorHandler;
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

        /**
         * @var Request $request
         * The global request object is initialized here.
         * "onRequest" event is dispatched.
         */
        global $request;
        $request = Request::createFromGlobals();
        EventDispatcher::dispatch('KernelListener', 'onRequest', [&$request]);

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
        $this->requestFirewall();
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
         * @var Request $request
         * @var FileSystemTransient $transient
         * Try to load route data from cache
         */
        global $request;
        $transient = new FileSystemTransient('vct-route-{' . $request->getPathInfo() . '}');
        if (!$transient->isValid()) {
            return;
        }
        $cacheData = $transient->getData();
        $httpMethods = unserialize($cacheData['methods']);

        /**
         * @var array|null $matches
         * @var array $params
         * Match request against route regex and allowed requests methods,
         * retrive matched params if any were passed on the request.
         */
        $matches = null;
        $params = [];
        if (!in_array($request->getMethod(), $httpMethods)) {
            return;
        }
        if (!preg_match_all($cacheData['regex'], $request->getPathInfo(), $matches)) {
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
         * @var callable $method
         * Execute controller callback, send the response and die.
         * "onControllerCallback" and "onResponse" events are dispatched.
         */
        $controller = new $cacheData['controller'](true);
        $method = $cacheData['callback'];
        EventDispatcher::dispatch('KernelListener', 'onControllerCallback', [&$request, $controller, $method, &$params]);
        $response = call_user_func_array([$controller, $method], [$request, $params]);
        EventDispatcher::dispatch('KernelListener', 'onResponse', [&$request, &$response]);

        $response->prepare($request);
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
         * Recursively initialize controllers, request will be parsed trough the Router instance.
         */
        $dir = new RecursiveDirectoryIterator(self::getProjectRoot() . 'src/controllers');
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            $fname = $file->getFilename();
            if (preg_match("%\.php$%", $fname)) {
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
         * Loads global configuration.
         * "onConfigurationLoaded" event is dispatched.
         */
        global $config;
        $transient = new FileSystemTransient('vct-config');
        if ($transient->isValid()) {
            $data = $transient->getData();
        } else {
            $path = self::getProjectRoot() . 'config/config.json';
            $data = json_decode(file_get_contents($path));
            $transient->setData($data);
        }
        EventDispatcher::dispatch('KernelListener', 'onConfigurationLoaded', [&$data]);
        $config = $data;

    }

    /**
     * @package Vector
     * Vector\Kernel->errorShutdown()
     * @return void
     */
    protected function registerShutdownFunctions(): void
    {

        /**
         * @var ErrorHandler $errorHandler
         * Errors, Exceptions and Shutdowns are delegated to the ErrorHandler class.
         */
        $errorHandler = new ErrorHandler();
        set_error_handler([$errorHandler, 'handleError']);
        set_exception_handler([$errorHandler, 'handleException']);
        register_shutdown_function([$errorHandler, 'handleShutdown']);

    }

    /**
     * @package Vector
     * Vector\Kernel->requestFirewall()
     * @return void
     */
    protected function requestFirewall(): void
    {

        /**
         * @var Request $request
         * @var Firewall $firewall
         * Request is passed through application Firewall for approval.
         * "onRequestVerified" event is dispatched.
         */
        global $request;
        $firewall = new Firewall();
        try {
            $firewall->verifyRequest($request);
        } catch (SecurityException) {
            $response = new Response(null, Response::HTTP_BAD_REQUEST);
            $response->prepare($request);
            $response->send();
            die();
        }
        EventDispatcher::dispatch('KernelListener', 'onRequestVerified', [&$request]);

    }

    /**
     * @package Vector
     * Vector\Kernel::getProjectRoot()
     * @return string
     */
    public static function getProjectRoot(): string
    {
        $workingDir = getcwd();
        if (str_contains($workingDir, 'public')) {
            return $workingDir . '/../';
        }
        return $workingDir . '/';
    }

    /**
     * @package Vector
     * Vector\Kernel::getAbsoluteUrl()
     * @param Request $request
     * @return string
     */
    public static function getAbsoluteUrl(Request $request): string
    {
        $scheme = $request->getScheme();
        $host = $request->getHttpHost();
        $uri = $request->getRequestUri();
        return $scheme . '://' . $host . $uri;
    }

}
