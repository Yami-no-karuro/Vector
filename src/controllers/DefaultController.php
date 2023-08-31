<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Vector\Module\ApplicationLogger\SqlLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class DefaultController extends FrontendController
{
    protected function register(): void
    {
        Router::route(['GET'], '^/?$', [$this, 'defaultAction']);
        Router::route(['GET'], '^/not-found?$', [$this, 'notFoundAction']);
    }

    /**
     * Route: '/'
     * Methods: GET
     * @return Response
     */
    public function defaultAction(): Response
    {

        /**
         * @var string $html
         * Builds the view raw html.
         */
        $html = $this->template->render('default.html.twig', [
            'title' => 'Vector',
            'description' => 'A simple HttpFoundation framework for PHP.'
        ]);

        return new Response($html, Response::HTTP_OK);
    }

    /**
     * Route: '/not-found'
     * Methods: GET
     * @param Request $request
     * @return Response
     */
    public function notFoundAction(Request $request): Response
    {

        /**
         * @var SqlLogger $logger
         * @var string $clientIp
         * Logging 404s to keep track of user and bot activities.
         */
        $logger = new SqlLogger('auth');
        if (null !== ($clientIp = $request->getClientIp())) {
            $logger->write('Client: "' . $clientIp . '" attempted to navigate an unknown route.');
        }

        /**
         * @var string $html
         * Builds the view raw html.
         */
        $html = $this->template->render('not-found.html.twig', [
            'title' => 'Vector',
            'description' => 'A simple HttpFoundation framework for PHP.'
        ]);

        return new Response($html, Response::HTTP_OK);
    }

}
