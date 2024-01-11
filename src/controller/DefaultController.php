<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Vector\Module\Storage\MediaStreamer;
use Vector\Module\ApplicationLogger\SqlLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
         * @var string $filepath
         * @var ?array $stream
         * Retrives the resource handle via MediaStreamer.
         */
        $filepath = '/' . $params['path'];
        if (null !== ($stream = MediaStreamer::getStream($filepath))) {
            $headers['Content-Type'] = $stream['mimeType'];
            $headers['Content-Length'] = $stream['fileSize'];
            return new StreamedResponse(function() use ($stream) {
                fpassthru($stream['handle']);
            }, Response::HTTP_OK, $headers);
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
