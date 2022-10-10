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
     * Vector\Controllers\ExampleController->init
     */
    private function init(): void {

        $this->router->register_route(['GET'], '^/?$', function(): Response {
            return new Response('<h2> Hello, World! </h2>', [
                'HTTP/1.1 200 OK',
                'Content-Type: text/html'
            ]);
        });

        $this->router->register_route(['GET'], '^/posts/?$', function(): Response {
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

        $this->router->register_route(['GET'], '^/posts/(?<id>\d+)/?$', function($params): Response {
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

        $this->router->register_route(['POST'], '^/posts/?$', function(): Response {
            $req_body = file_get_contents('php://input');
            return new Response($req_body, [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

        $this->router->register_route(['GET'], '^/search-posts/?$', function(): Response {
            $mysql = MySqlConnect::get_instance();
            $posts = array();
            if (isset($_GET['search'])) {
                $posts = $mysql->get_results("SELECT * FROM `wp_posts` WHERE 
                    `post_title` LIKE ? OR
                    `post_content` LIKE ?", array(
                        ['type' => 's', 'value' => '%' . $_GET['search'] . '%'],
                        ['type' => 's', 'value' => '%' . $_GET['search'] . '%']
                ));
            }
            if (false === $posts['success']) { return new Response(NULL, ['HTTP/1.1 500 Internal Server Error']); }
            return new Response(json_encode($posts['data']), [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

    }
    
}

new ExampleController();