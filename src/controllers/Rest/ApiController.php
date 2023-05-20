<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\RestController;
use Vector\Module\RateLimiter;
use Vector\Module\RateExceededException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class ApiController extends RestController {

    protected function register(): void
    {
        Router::route(['GET'], '^/api?$', [$this, 'apiAction']);
    }

    /**
     * Route '/api'
     * Api endpoint
     * @param Request
     * @return JsonResponse
     */
    public function apiAction(Request $request): JsonResponse
    {

        /** Limit requests on this route to 120 per minute per IP address */
        $rateLimiter = new RateLimiter($request, 'api-route-rate');
        try {
            $rateLimiter->limitRequestsInMinutes(120, 1);
        } catch (RateExceededException) {
            return new Response(null, Response::HTTP_TOO_MANY_REQUESTS);
        }

        /** Return the JsonResponse object */
        return new JsonResponse(['success' => true], Response::HTTP_OK);
        
    }
    
}
