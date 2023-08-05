<?php

namespace Vector;

use Vector\Module\Security\Firewall;
use Vector\Module\Security\SecurityException;
use Vector\Module\Security\UnauthorizedException;
use Vector\Module\Transient\FileSystemTransient;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\ApplicationLogger\SqlLogger;
use Vector\Module\Event\EventDispatcher;
use Vector\Module\ErrorHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Kernel
{
    protected Request $request;
    protected FileSystemLogger $logger;
    protected SqlLogger $sqlLogger;

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

        $this->loadConfig();
        $this->logger = new FileSystemLogger('core');
        $this->sqlLogger = new SqlLogger('auth');
    }

    /**
     * @package Vector
     * Vector\Kernel->boot()
     * @return void
     */
    public function boot(): void
    {
        $this->verifyRequest();
        $this->registerShutdownFunctions();
        $this->handleCallback();
        $this->routeRegister();
    }

    /**
     * @package Vector
     * Vector\Kernel->handleCallback()
     * @return void
     */
    protected function handleCallback(): void
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
         * "onCallback" and "onResponse" events are dispatched.
         */
        $controller = new $cacheData['controller'](true);
        $method = $cacheData['callback'];
        EventDispatcher::dispatch('KernelListener', 'onCallback', [&$request, $controller, $method, &$params]);
        $response = call_user_func_array([$controller, $method], [$request, $params]);
        EventDispatcher::dispatch('KernelListener', 'onResponse', [&$request, &$response]);

        $response->prepare($request);
        $response->send();
        die();

    }

    /**
     * @package Vector
     * Vector\Kernel->routeRegister()
     * @return void
     */
    protected function routeRegister(): void
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
         * "onConfiguration" event is dispatched.
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

        EventDispatcher::dispatch('KernelListener', 'onConfiguration', [&$data]);
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
     * Vector\Kernel->verifyRequest()
     * @return void
     */
    protected function verifyRequest(): void
    {

        /**
         * @var Request $request
         * @var Firewall $firewall
         * Request is passed through application Firewall for approval.
         */
        global $request;
        $firewall = new Firewall();
        try {
            $firewall->verifyRequest($request);
        } catch (Exception $e) {
            if ($e instanceof SecurityException) {
                $response = new Response(null, Response::HTTP_UNAUTHORIZED);
                $this->sqlLogger->write('Client: "' . $request->getClientIp() . '" request contained malicious content.');
            } elseif ($e instanceof UnauthorizedException) {
                $response = new RedirectResponse('/login', Response::HTTP_FOUND);
                $this->sqlLogger->write('Client: "' . $request->getClientIp() . '" attempted to reach a secure route without being authenticated.');
            }
            $response->prepare($request);
            $response->send();
            die();
        }

    }

    /**
     * @package Vector
     * Vector\Kernel::getProjectRoot()
     * @return string
     */
    public static function getProjectRoot(): string
    {

        /**
         * @var string $workingDir
         * Retriving workdir based on the execution mode. 
         */
        $workingDir = getcwd();
        if (str_contains($workingDir, 'public')) {
            return $workingDir . '/../';
        }
        
        return $workingDir . '/';
    }

    /**
     * @package Vector
     * Vector\Kernel::getRequestUrl()
     * @param Request $request
     * @return string
     */
    public static function getRequestUrl(Request $request): string
    {

        /**
         * @var object $config
         * Preparing URL based on the machine envoirment.
         * If the project is running on Docker the internal host will be used.
         */
        global $config;
        if (true === $config->dockerized) {
            return 'http://php-apache:80' . $request->getRequestUri();
        }

        /**
         * @var string $host
         * @var string $port
         * @var string $scheme
         * If the project is hosted natively we retrive url based on request informations.
         */
        $host = $request->getHost();
        $port = $request->getPort();
        $scheme = $request->getScheme();
        return $scheme . '://' . $host . ($port ? ':' . $port : '') . $request->getRequestUri();

    }

}
