<?php
namespace Vector\Controllers;
use Vector\Router;
use Vector\Objects\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    die(); 
}

class ExampleController {

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

        /* List Posts */
        $this->router->register_route(['GET'], '^/posts/?$', function() {
            $postlist = json_encode([
                [
                    'ID' => 1,
                    'Title' => 'Lorem Ipsum',
                    'Content' => 'Lorem Ipsum Dolor Sit'
                ],
                [
                    'ID' => 2,
                    'Title' => 'Lorem Ipsum',
                    'Content' => 'Lorem Ipsum Dolor Sit'
                ]
            ]);
            return new Response($postlist, [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

        /* Find Post */
        $this->router->register_route(['GET'], '^/posts/(?<id>\d+)/?$', function($params) {
            $post = json_encode([
                'ID' => $params['id'],
                'Title' => 'Lorem Ipsum',
                'Content' => 'Lorem Ipsum Dolor Sit'
            ]); 
            return new Response($post, [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

        /* Create Post */
        $this->router->register_route(['POST'], '^/posts/?$', function() {
            $req_body = file_get_contents('php://input');
            return new Response($req_body, [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

    }
    
}

new ExampleController();