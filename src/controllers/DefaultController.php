<?php

namespace Vector\Controller;

use Vector\Module\AbstractController;
use Vector\Module\RateLimiter;
use Vector\Module\RateExceededException;
use Vector\Module\Transient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        /** Return the Response object */
        return new Response($html, Response::HTTP_OK);
        
    }

    /**
     * Route '/json'
     * Json response
     * @return JsonResponse
     */
    public function jsonAction(): JsonResponse
    {

        /** Check if any cached data is available, data is considered valid under 900s */
        $transient = new Transient('json-response');
        if ($transient->isValid(900)) {
            $data = $transient->getContent();
        } else {
            $data = [['foo' => 'bar'], ['fizz' => 'buzz']];
            $transient->setContent($data);
        }

        /** Return the Response object */
        return new JsonResponse($data, Response::HTTP_OK);
    }
    
}
