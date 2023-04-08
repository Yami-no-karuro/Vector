<?php

namespace Vector\Controller;

use Vector\Module\AbstractController;
use Vector\Module\Transient;
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

            /**
             * @var RateLimiter $rateLimiter
             * Limits the number of requests to this endpoint to 60 per minute per IP address.
             */
            $rateLimiter = new RateLimiter($request);
            try {
                $rateLimiter->limitRequestsInMinutes(60, 1);
            } catch (RateExceededException) {
                return new Response(null, Response::HTTP_TOO_MANY_REQUESTS);
            }
            
            /** 
             * @var Transient $exampleTransient
             * Cache the result of $this->resourceExpensiveFunction().
             * Transient is considered valid if newer that 900 seconds.
             */
            $exampleTransient = new Transient('example');
            if ($exampleTransient->isValid(900)) {
                $result = $exampleTransient->getContent();
            } else {
                $result = $this->resourceExpensiveFunction();
                $exampleTransient->setContent($result);
            }

            /**
             * @var string $html
             * Parse example.html.twig template and store the result in $html as string.
             */
            $html = $this->template->render('example.html.twig', [
                'title' => 'Vector',
                'description' => 'A simple yet performing PHP framework',
                'someData' => $result
            ]);

            /** Return a response object */
            return new Response($html, Response::HTTP_OK);

        });

    }

    /**
     * Example..
     * Let's pretend this function has to do some very expensive work to retrive data.
     * @return string 
     */
    protected function resourceExpensiveFunction(): string 
    {
        return json_encode([ 'message' => 'Hello, World!' ]);
    }
    
}
