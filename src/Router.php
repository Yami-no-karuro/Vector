<?php
namespace Vector;

use Vector\Entities\Request;
use Vector\Entities\Response;
use Vector\Engine\RateLimiter;
use Vector\Engine\RateExceededException;
use Vector\Engine\EventDispatcher;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Router {

    private static mixed $instance = null;
   
    /**
     * @package Vector
     * Vector\Router::get_instance()
     * @return Router
     */
    public static function get_instance(): Router {
        if (self::$instance == null) { self::$instance = new Router(); }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Router->register_route()
     * @param {array} $http_methods
     * @param {string} $route
     * @param {callable} $callback
     * @param {int} $rpm = 60
     * @param {bool} $die = true
     * @return void
     */
    public function register_route(array $http_methods, string $route, callable $callback, int $rpm = 120, bool $die = true): void {
        $request = new Request();
        $rate_limiter = new RateLimiter($request->remote_address);
        $seconds = floor(1 * $rpm);
        try {
            $rate_limiter->limit_requests_in_minutes($rpm, 1);
        } catch (RateExceededException $e) {
            $response = new Response(NULL, ['HTTP/1.1 429 Too Many Requests']);
            $response->send(true);
        }
        $request_event = new EventDispatcher('OnRequest');
        $request_event->dispatch([$request]);
        static $path = null;
        if ($path === null) {
            $path = parse_url($request->request_uri)['path'];
            $script_name = dirname(dirname($request->script_name));
            $script_name = str_replace('\\', '/', $script_name);
            $len = strlen($script_name);
            if ($len > 0 && $script_name !== '/') { $path = substr($path, $len); }
        }
        if (!in_array($request->request_method, (array) $http_methods)) { return; }
        $matches = null;
        $regex = '/' . str_replace('/', '\/', $route) . '/';
        if (!preg_match_all($regex, $path, $matches)) { return; }
        $params = array();
        if (!empty($matches)) {
            foreach ($matches as $k => $v) { 
                if (!is_numeric($k) && !isset($v[1])) { 
                    $params[$k] = $v[0]; 
                } 
            }
        }
        $response = $callback($request, $params);
        $response_event = new EventDispatcher('OnResponse');
        $response_event->dispatch([$request, $response]);
        $response->send();
        if ($die) { die(); }
    }

}
