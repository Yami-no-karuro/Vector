<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\DataObject\Asset;
use Vector\Module\Controller\FrontendController;
use Vector\Module\ApplicationLogger\SqlLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vector\Repository\AssetRepository;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class DefaultController extends FrontendController
{

    protected function register(): void
    {
        Router::route(['GET'], '^/?$', [$this, 'defaultAction']);
        Router::route(['GET'], '^/storage/(?<path>.+)$', [$this, 'storageAction']);
        Router::route(['GET'], '^/not-found/?$', [$this, 'notFoundAction']);
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
     * Route: '/storage/<path>'
     * Methods: GET
     * @param Request $request
     * @param array $params
     * @return Response
     */
    public function storageAction(Request $request, array $params): Response
    {

        /**
         * @var AssetRepository $repository
         * @var Asset $asset
         * The asset repository is retrived.
         * If the media actually exists the raw resource handler is retrived. 
         */
        $repository = AssetRepository::getInstance();
        if (null !== ($asset = $repository->getByPath($params['path']))) {
            $stream = $asset->getStream();
            if (is_resource($stream)) {
                return new StreamedResponse(function() use ($stream) {
                    fpassthru($stream);
                }, Response::HTTP_OK, [
                    'Content-Type' => $asset->getMimeType(),
                    'Content-Length' => $asset->getSize()
                ]);
            }
        }

        return new Response(null, Response::HTTP_NOT_FOUND);
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
        $logger->write('Client: "' . $request->getClientIp() . '" attempted to navigate an unknown route.');

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
