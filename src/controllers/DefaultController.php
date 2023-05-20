<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\FrontendController;
use Vector\Module\RateLimiter;
use Vector\Module\RateExceededException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class DefaultController extends FrontendController {

    protected function register(): void
    {
        Router::route(['GET'], '^/?$', [$this, 'defaultAction']);
    }

    /**
     * Route '/'
     * Twig template
     * @param Request
     * @return Response
     */
    public function defaultAction(Request $request): Response
    {

        /** Limit requests on this route to 120 per minute per IP address */
        $rateLimiter = new RateLimiter($request, 'default-route-rate');
        try {
            $rateLimiter->limitRequestsInMinutes(120, 1);
        } catch (RateExceededException) {
            return new Response(null, Response::HTTP_TOO_MANY_REQUESTS);
        }

        /** Render view and save the result in $html */
        $html = $this->template->render('default.html.twig', [
            'title' => 'Vector',
            'description' => 'A simple HttpFoundation framework for PHP.'
        ]);

        /** Return the Response object */
        return new Response($html, Response::HTTP_OK);
        
    }
    
}
