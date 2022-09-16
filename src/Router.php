<?php
namespace Vector;
use Vector\Functions\RateLimiter;
use Vector\Functions\RateExceededException;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    echo '403 Forbidden';
    die(); 
}

class Router {

    private static $instance = null;
   
    /**
     * @package Vector
     * Vector\Router::get_instance()
     */
    public static function get_instance(): object {
        if (self::$instance == null) { self::$instance = new Router(); }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Router->register_route()
     * @param {[string]} $http_methods
     * @param {string} $route
     * @param {function} $callback
     * @param {int} $rpm
     * @param {bool} $die
     * @return void
     */
    public function register_route(array $http_methods, string $route, callable $callback, int $rpm = 60, bool $die = true): void {
        $rate_limiter = new RateLimiter($_SERVER['REMOTE_ADDR']);
        $seconds = floor(1 * $rpm);
        try {
            $rate_limiter->limit_requests_in_minutes($rpm, 1);
        } catch (RateExceededException $e) {
            header('HTTP/1.1 429 Too Many Requests');
            header(sprintf("Retry-After: %d", $seconds));
            echo '429 Rate Limit Exceeded';
            die();
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
            foreach ($response->headers as $header) { header($header); }
            echo $response->body;
        } else {
            $params = array();
            foreach ($matches as $k => $v) {
                if (!is_numeric($k) && !isset($v[1])) { $params[$k] = $v[0]; }
            }
            $response = $callback($params);
            foreach ($response->headers as $header) { header($header); }
            echo $response->body;
        }
        if ($die) { die(); }
    }

}
