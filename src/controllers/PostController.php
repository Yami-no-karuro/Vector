<?php
namespace Vector\Controllers;
use Vector\Router;
use Vector\Objects\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    echo '403 Forbidden';
    die(); 
}

class PostController {

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
        
        /* TEST */
        $this->router->register_route(['GET', 'POST'], '^/test/?$', function() {            
            $request_body = json_decode(file_get_contents('php://input'), true);
            return new Response(json_encode($request_body), [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

    }
    
}

new PostController();