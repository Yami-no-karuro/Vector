<?php

namespace Vector\Controller\Admin;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class DashboardController extends FrontendController
{

    protected function register(): void
    {
        Router::route(['GET'], '^/admin/?$', [$this, 'dashboardViewAction']);
    }

    /**
     * Route: '/admin'
     * Methods: GET
     * @return Response
     */
    public function dashboardViewAction(): Response
    {

        /**
         * @var string $html
         * Builds the view raw html.
         */
        $html = $this->template->render('admin/admin.html.twig', [
            'title' => 'Vector - Dashboard',
            'description' => ''
        ]);

        return new Response($html, Response::HTTP_OK);
    }

}
