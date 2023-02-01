<?php
namespace Vector\Controllers;

use Vector\Objects\Request;
use Vector\Objects\Response;
use Vector\Engine\Controller;
use Vector\Engine\Transient;
use Vector\Engine\DBC;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class ExampleController extends Controller {

    protected function init(): void {

        /**
         * GET "/"
         * Twig Template
         */
        $this->router->register_route(['GET'], '^/?$', function(Request $request): Response {
            $html = $this->template->render('home.html.twig', array(
                'title'       => 'Vector',
                'description' => 'A simple yet performing PHP framework'
            ));
            return new Response($html, [
                'HTTP/1.1 200 OK',
                'Content-Type: text/html'
            ]);
        });

        /**
         * GET "/posts/"
         * Cached JSON Response (Refresh Time: 900s)
         */
        $this->router->register_route(['GET'], '^/posts/?$', function(Request $request): Response {
            $mysql = DBC::get_instance();
            if (isset($_GET['search'])) {
                $posts = $mysql->get_results("SELECT * FROM `wp_posts` WHERE 
                    `post_title` LIKE ? OR
                    `post_content` LIKE ?", array(
                        ['type' => 's', 'value' => '%' . $_GET['search'] . '%'],
                        ['type' => 's', 'value' => '%' . $_GET['search'] . '%']
                ));
            } else { $posts = $mysql->get_results("SELECT * FROM `wp_posts`"); }
            if (false === $posts['success']) { return new Response(NULL, ['HTTP/1.1 500 Internal Server Error']); }
            $transient = new Transient('posts');
            $transient_data = $transient->get_data(900);
            if (false === $transient_data->valid) {
                $resp = json_encode($posts['data']);
                $transient->set_data($resp);
            } else { $resp = $transient_data->content; }
            return new Response($resp, [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

        /**
         * GET "/posts/<id>"
         * JSON Response
         */
        $this->router->register_route(['GET'], '^/posts/(?<id>\d+)/?$', function(Request $request, $params): Response {
            $mysql = DBC::get_instance();
            $posts = $mysql->get_results("SELECT * FROM `wp_posts` WHERE `ID` = ?", array(
                ['type' => 'i', 'value' => $params['id']]
            ));
            if (false === $posts['success']) { return new Response(NULL, ['HTTP/1.1 500 Internal Server Error']); }
            return new Response(json_encode($posts['data']), [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

        /**
         * POST "/posts/"
         * JSON Response
         */
        $this->router->register_route(['POST'], '^/posts/?$', function(Request $request): Response {
            $req_body = file_get_contents('php://input');
            return new Response($req_body, [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

    }
    
}

new ExampleController();