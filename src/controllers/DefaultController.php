<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;
use Vector\Module\Transient\RedisTransient;

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
        $html = $this->template->render('default.html.twig', [
            'title' => 'Vector',
            'description' => 'A simple HttpFoundation framework for PHP.'
        ]);

        $transient = new RedisTransient('default');
        if ($transient->isValid()) {
            $html = $transient->getData();
        } else {
            $html = $this->template->render('default.html.twig', [
                'title' => 'Vector',
                'description' => 'A simple HttpFoundation framework for PHP.'
            ]);
            $transient->setData($html, 900);
        }

        return new Response($html, Response::HTTP_OK);
    }

    /**
     * Route: '/not-found'
     * Methods: GET
     * @return Response
     */
    public function notFoundAction(): Response
    {
        $html = $this->template->render('not-found.html.twig', [
            'title' => 'Vector',
            'description' => 'A simple HttpFoundation framework for PHP.'
        ]);
        return new Response($html, Response::HTTP_OK);
    }

}
