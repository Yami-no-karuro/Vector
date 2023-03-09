<?php

namespace Vector\Controllers;

use Vector\Module\Controller;
use Vector\Module\SqlConnection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class ExampleController extends Controller {

    protected function init(): void {

        /**
         * GET "/"
         * Twig Template example
         */
        $this->router->registerRoute(['GET'], '^/?$', function(Request $request): Response 
        {
            $html = $this->template->render('home.html.twig', [
                'title' => 'Vector',
                'description' => 'A simple yet performing PHP framework'
            ]);
            return new Response($html, Response::HTTP_OK, [
                'Content-Type: text/html'
            ]);
        });

    }
    
}