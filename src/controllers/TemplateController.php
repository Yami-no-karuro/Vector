<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class TemplateController extends FrontendController
{
    protected function register(): void
    {
        Router::route(['GET'], '^/?$', [$this, 'templateAction']);
    }

    /**
     * Route '/'
     * Twig template
     * @return Response
     */
    public function templateAction(): Response
    {
        $html = $this->template->render('default.html.twig', [
            'title' => 'Vector',
            'description' => 'A simple HttpFoundation framework for PHP.'
        ]);
        return new Response($html, Response::HTTP_OK);
    }
}
