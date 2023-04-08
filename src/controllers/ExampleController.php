<?php

namespace Vector\Controller;

use Vector\Module\AbstractController;
use Vector\Module\RateLimiter;
use Vector\Module\RateExceededException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class ExampleController extends AbstractController {

    protected function init(): void 
    {

        /**
         * GET "/"
         * Twig Template example
         */
        $this->router->registerRoute(['GET'], '^/?$', function(Request $request): Response 
        {
            return new Response($this->template->render('example.html.twig', [
                'title' => 'Vector',
                'description' => 'A simple yet performing PHP framework'
            ]), Response::HTTP_OK);
        });

    }
    
}
