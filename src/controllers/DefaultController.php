<?php

namespace Vector\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Vector\Module\AbstractController;
use Vector\Module\RateLimiter;
use Vector\Module\RateExceededException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class DefaultController extends AbstractController {

    protected function init(): void 
    {
        $this->router->registerRoute(['GET'], '^/?$', [$this, 'defaultAction']);
        $this->router->registerRoute(['GET'], '^/json/?$', [$this, 'jsonAction']);
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

        /** Return the Response */
        return new Response($html, Response::HTTP_OK);
        
    }

    /**
     * Route '/json'
     * Json response
     * @return JsonResponse
     */
    public function jsonAction(): JsonResponse
    {
        return new JsonResponse(['foo' => 'bar']);
    }
    
}
