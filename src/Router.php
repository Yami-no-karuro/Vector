<?php

namespace Vector;

use Vector\Kernel;
use Vector\Module\Transient\FileSystemTransient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Router
{
    /**
     * @package Vector
     * Vector\Router->route()
     * @param array $httpMethods
     * @param string $route
     * @param callable $callback
     * @return void
     */
    public static function route(array $httpMethods, string $route, callable $callback): void
    {

        /**
         * @var Request $request
         * Retrive the global request object initialized in the Kernel.
         */
        global $request;

        /**
         * @var array|null $matches
         * @var array $params
         * @var string $regex
         * Match request against route regex and allowed requests methods,
         * retrive matched params if any were passed on the request.
         */
        $matches = null;
        $params = [];
        $regex = '/' . str_replace('/', '\/', $route) . '/';
        if (!in_array($request->getMethod(), (array) $httpMethods)) {
            return;
        }
        if (!preg_match_all($regex, $request->getPathInfo(), $matches)) {
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
         * @var object $controller
         * @var callabled $method
         * @var FileSystemTransient $transient
         * Cache route data for future requests.
         */
        $controller = get_class($callback[0]);
        $method = $callback[1];
        $transient = new FileSystemTransient('vct-route-{' . $request->getPathInfo() . '}');
        $transient->setData([
            'path' => $request->getPathInfo(),
            'regex' => $regex,
            'methods' => serialize($httpMethods),
            'controller' => $controller,
            'callback' => $method
        ]);

        /**
         * @var Response $response
         * @var Request $request
         * Now that the route has been registered force a new internal request to same route to trigger Kernel direct boot.
         * Redirect is necessary to keep only one application exitpoint.
         */
        $client = HttpClient::create();
        $response = $client->request($request->getMethod(), Kernel::getRequestUrl($request), [
            'headers' => $request->headers->all(),
            'body' => $request->getContent()
        ]);

        /**
         * @var int $statusCode
         * @var array $headers
         * @var string $body
         * Retrive response information from the internal request.
         * A new Response object is created and sent.
         */
        $statusCode = $response->getStatusCode();
        $headers = $response->getHeaders();
        $body = $response->getContent();
        $response = new Response($body, $statusCode, $headers);
        $response->prepare($request);
        $response->send();
        die();

    }

}
