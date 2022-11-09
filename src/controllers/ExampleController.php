<?php
namespace Vector\Controllers;
use Vector\Router;
use Vector\Engine\Controller;
use Vector\Engine\TemplateEngine;
use Vector\Engine\MySqlConnect;
use Vector\Engine\Transient;
use Vector\Objects\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class ExampleController extends Controller {

    protected function init(): void {

        $this->router->register_route(['GET'], '^/?$', function(): Response {
            $transient = new Transient('home');
            $transient_data = $transient->get_data(900);
            if (false === $transient_data->valid) {
                $template = new TemplateEngine('home', array('pagename' => 'Vector'));
                $html = $template->parse();
                $transient->set_data($html);
            } else { $html = $transient_data->content; }
            return new Response($html, [
                'HTTP/1.1 200 OK',
                'Content-Type: text/html'
            ]);
        });

        $this->router->register_route(['GET'], '^/posts/?$', function(): Response {
            $mysql = MySqlConnect::get_instance();
            if (isset($_GET['search'])) {
                $posts = $mysql->get_results("SELECT * FROM `wp_posts` WHERE 
                    `post_title` LIKE ? OR
                    `post_content` LIKE ?", array(
                        ['type' => 's', 'value' => '%' . $_GET['search'] . '%'],
                        ['type' => 's', 'value' => '%' . $_GET['search'] . '%']
                ));
            } else { $posts = $mysql->get_results("SELECT * FROM `wp_posts`"); }
            if (false === $posts['success']) { return new Response(NULL, ['HTTP/1.1 500 Internal Server Error']); }
            return new Response(json_encode($posts['data']), [
                'HTTP/1.1 200 OK',
                'Content-Type: application/json'
            ]);
        });

        $this->router->register_route(['GET'], '^/posts/(?<id>\d+)/?$', function($params): Response {
            $mysql = MySqlConnect::get_instance();
            $posts = $mysql->get_results("SELECT * FROM `wp_posts` WHERE `ID` = ?", array(
                ['type' => 'i', 'value' => $params['id']]
            ));
            if (false === $posts['success']) { return new Response(NULL, ['HTTP/1.1 500 Internal Server Error']); }
            return new Response(json_encode($posts['data']), [
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

    }
    
}

new ExampleController();