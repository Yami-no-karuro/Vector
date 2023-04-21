<?php

namespace Vector\Controller;

use Vector\Module\AbstractController;
use Vector\Module\RateLimiter;
use Vector\Module\RateExceededException;
use Vector\Module\Transient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class DefaultController extends AbstractController {

    protected function init(): void 
    {

        /**
         * GET "/"
         * Twig Template example
         */
        $this->router->registerRoute(['GET'], '^/?$', function(Request $request): Response 
        {

            $rateLimiter = new RateLimiter($request, 'default-route-rate');
            try {
                $rateLimiter->limitRequestsInMinutes(120, 1);
            } catch (RateExceededException) {
                return new Response(null, Response::HTTP_TOO_MANY_REQUESTS);
            }

            $transient = new Transient('example');
            if ($transient->isValid(0)) {
                $data = json_decode($transient->getContent());
            } else { 
                $data = $this->exampleFuntion();
                $transient->setContent(json_encode($data));
            }

            $html = $this->template->render('default.html.twig', [
                'title' => 'Vector',
                'description' => 'A simple HttpFoundation framework for PHP.'
            ]);

            return new Response($html, Response::HTTP_OK);

        });

    }

    /** Let's pretend this function has to do a lot of work to retrive some data.. */
    protected function exampleFuntion(): array 
    {
        return ['some' => 'data'];
    }
    
}
