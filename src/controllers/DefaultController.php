<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
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
        Router::route(['GET'], '^/not-found?$', [$this, 'errorAction']);
    }

    /**
     * Route: '/'
     * Methods: GET
     * @return Response
     */
    public function defaultAction(): Response
    {
        $html = $this->template->render('default.html.twig', [
            'title' => 'Vector',
            'description' => 'A simple HttpFoundation framework for PHP.'
        ]);
        return new Response($html, Response::HTTP_OK);
    }

    /**
     * Route: '/error'
     * Methods: GET
     * @return Response
     */
    public function errorAction(): Response
    {
        $html = $this->template->render('error.html.twig', [
            'title' => 'Vector',
            'description' => 'A simple HttpFoundation framework for PHP.'
        ]);
        return new Response($html, Response::HTTP_OK);
    }

}
