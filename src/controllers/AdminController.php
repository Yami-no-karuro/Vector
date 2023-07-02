<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class AdminController extends FrontendController
{
    protected function register(): void
    {
        Router::route(['GET'], '^/admin?$', [$this, 'adminViewAction']);
    }

    /**
     * Route '/admin'
     * Twig template
     * @return Response
     */
    public function adminViewAction(): Response
    {
        $html = $this->template->render('admin/admin.html.twig', [
            'title' => 'Vector - Admin',
            'description' => 'Vector administration area.'
        ]);
        return new Response($html, Response::HTTP_OK);
    }

}