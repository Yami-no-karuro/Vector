<?php
namespace Vector;
use Vector\Objects\Response;
use Vector\Engine\RateLimiter;
use Vector\Engine\RateExceededException;

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
     * @param {array} $allowed_origins
     * @param {array} $allowed_headers
     * @param {int} $rpm
     * @param {bool} $die
     * @return void
     */
    public function register_route(array $http_methods, string $route, callable $callback, array $allowed_origins = [], array $allowed_headers = [], int $rpm = 60, bool $die = true): void {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            if (!empty($allowed_origins)) {
                $origins = implode(', ', $allowed_origins);
                header("Access-Control-Allow-Origin: {$origins}");     
            } else { header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}"); }
        }
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) { 
                $methods = implode(', ', $http_methods);
                header("Access-Control-Allow-Methods: {$methods}"); 
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) { 
                if (!empty($allowed_headers)) {
                    $headers = implode(', ', $allowed_headers);
                    header("Access-Control-Allow-Headers: {$headers}");
                } else { header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}"); }
            }
            die();
        }
        $rate_limiter = new RateLimiter($_SERVER['REMOTE_ADDR']);
        $seconds = floor(1 * $rpm);
        try {
            $rate_limiter->limit_requests_in_minutes($rpm, 1);
        } catch (RateExceededException $e) {
            $response = new Response(NULL, ['HTTP/1.1 429 Too Many Requests']);
            $response->send(true);
        }
        static $path = null;
        if ($path === null) {
            $path = parse_url($_SERVER['REQUEST_URI'])['path'];
            $script_name = dirname(dirname($_SERVER['SCRIPT_NAME']));
            $script_name = str_replace('\\', '/', $script_name);
            $len = strlen($script_name);
            if ($len > 0 && $script_name !== '/') { $path = substr($path, $len); }
        }
        if (!in_array($_SERVER['REQUEST_METHOD'], (array) $http_methods)) { return; }
        $matches = null;
        $regex = '/' . str_replace('/', '\/', $route) . '/';
        if (!preg_match_all($regex, $path, $matches)) { return; }
        if (empty($matches)) {
            $response = $callback();
            $response->send();
        } else {
            $params = array();
            foreach ($matches as $k => $v) {
                if (!is_numeric($k) && !isset($v[1])) { $params[$k] = $v[0]; }
            }
            $response = $callback($params);
            $response->send();
        }
        if ($die) { die(); }
    }

}
