<?php
namespace Vector\Controllers;
use Vector\Router;
use Vector\Objects\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    echo '403 Forbidden';
    die(); 
}

class BaseController {

    private $router;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct() {
        $this->router = Router::get_instance();
        $this->init();
    }

    /**
     * @package Vector
     * Vector\Controllers\ViewController->init
     */
    private function init() {
        
        /* Hello, World! */
        $this->router->register_route(['GET'], '^/?$', function() {
            return new Response('<h2> Hello, World! </h2>', [
                'HTTP/1.1 200 OK',
                'Content-Type: text/html'
            ]);
        });

    }
    
}
