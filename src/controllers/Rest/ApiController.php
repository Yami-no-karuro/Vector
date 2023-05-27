<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\RestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class ApiController extends RestController
{
    protected function register(): void
    {
        Router::route(['GET'], '^/api?$', [$this, 'apiAction']);
    }

    /**
     * Route '/api'
     * Api endpoint
     * @return JsonResponse
     */
    public function apiAction(): JsonResponse
    {
        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }

}
