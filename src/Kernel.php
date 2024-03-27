<?php

namespace Vector;

use Vector\Module\Security\Firewall;
use Vector\Module\Transient\SqlTransient;
use Vector\Module\EventDispatcher;
use Vector\Module\ErrorHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        global $request;

        $request = Request::createFromGlobals();
        EventDispatcher::dispatch('KernelListener', 'onRequest', [&$request]);

        $this->loadConfig();
        $this->loadErrorHandlers();
    }

    /**
     * @package Vector
     * Vector\Kernel->boot()
     * @return void
     */
    public function boot(): void
    {
        $this->verifyRequest();
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
        global $request;

        $transient = new SqlTransient('vct-route-{' . $request->getPathInfo() . '}');
        if (!$transient->isValid()) {
            return;
        }

        $cacheData = $transient->getData();
        $httpMethods = unserialize($cacheData['methods']);

        $matches = null;
        $params = [];

        if (!in_array($request->getMethod(), $httpMethods)) { return; }
        if (!preg_match_all($cacheData['regex'], $request->getPathInfo(), $matches)) { return; }
        if (!empty($matches)) {
            foreach ($matches as $key => $value) {
                if (!is_numeric($key) && !isset($value[1])) {
                    $params[$key] = $value[0];
                }
            }
        }

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
        $dir = new RecursiveDirectoryIterator(self::getProjectRoot() . 'src/Controller');
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {

            $fname = $file->getFilename();
            if (preg_match("%\.php$%", $fname)) {

                $controller = self::getClassNamespace($file->getPathname());
                if (class_exists($controller)) {
                    new $controller();
                }
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
        global $config;

        $path = self::getProjectRoot() . 'config/config.json';
        $data = json_decode(file_get_contents($path));

        EventDispatcher::dispatch('KernelListener', 'onConfiguration', [&$data]);
        $config = $data;
    }

    /**
     * @package Vector
     * Vector\Kernel->errorShutdown()
     * @return void
     */
    protected function loadErrorHandlers(): void
    {
        $handler = new ErrorHandler();
        EventDispatcher::dispatch('KernelListener', 'onErrorHandler', [&$handler]);

        set_error_handler([$handler, 'handleError']);
        set_exception_handler([$handler, 'handleException']);
        register_shutdown_function([$handler, 'handleShutdown']);
    }

    /**
     * @package Vector
     * Vector\Kernel->verifyRequest()
     * @return void
     */
    protected function verifyRequest(): void
    {
        global $request;

        $firewall = new Firewall();
        EventDispatcher::dispatch('KernelListener', 'onFirewall', [&$firewall]);

        try {
            $firewall->verifyRequest($request);
        } catch (Exception) {
            $response = new Response(null, Response::HTTP_UNAUTHORIZED);
            $response->prepare($request);
            $response->send();
            die();
        }
    }

    /**
     * @package Vector
     * Vector\Kernel::getNamespaceFromPath()
     * @param string $filepath
     * @param string $rootDirectory
     * @return ?string
     */
    public static function getClassNamespace(string $filepath, string $root = 'src'): string
    {
        $filepath = trim($filepath, '\\');
        if (!str_contains($filepath, $root)) {
            return null;
        }

        $path = explode('/', $filepath);
        $path[count($path) - 1] = pathinfo($path[count($path) - 1])['filename'];
        $namespace = array_slice($path, (array_search($root, $path) + 1));
        return implode('\\', ['\\Vector', ...$namespace]);
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
     * Vector\Kernel::getRequestUrl()
     * @param Request $request
     * @return string
     */
    public static function getRequestUrl(Request &$request): string
    {
        global $config;
        if (true === $config->dockerized) {
            return 'http://php-apache:80' . $request->getRequestUri();
        }

        $host = $request->getHost();
        $port = $request->getPort();
        $scheme = $request->getScheme();
        return $scheme . '://' . $host . ($port ? ':' . $port : '') . $request->getRequestUri();
    }

}
