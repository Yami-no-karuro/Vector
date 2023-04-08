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
            $rateLimiter = new RateLimiter($request);
            try {
                $rateLimiter->limitRequestsInMinutes(120, 1);
            } catch (RateExceededException $e) {
                return new Response(null, Response::HTTP_TOO_MANY_REQUESTS);
            }
            return new Response($this->template->render('home.html.twig', [
                'title' => 'Vector',
                'description' => 'A simple yet performing PHP framework'
            ]), Response::HTTP_OK);
        });

    }
    
}
