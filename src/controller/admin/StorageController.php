<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\DataObject\Asset;
use Vector\Module\Controller\FrontendController;
use Vector\Repository\AssetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class StorageController extends FrontendController
{

    protected function register(): void
    {
        Router::route(['GET'], '^/admin/storage?$', [$this, 'storageViewAction']);
        Router::route(['POST'], '^/admin/storage/upload?$', [$this, 'storageUploadAction']);
    }

    /**
     * Route: '/admin/storage'
     * Methods: GET
     * @param Request $request
     * @return Response
     */
    public function storageViewAction(Request $request): Response
    {

        /**
         * @var AssetRepository $repository
         * @var ?array<Asset> $assets 
         * Stored assets are retrived.
         */
        $repository = AssetRepository::getInstance();
        $assets = $repository->getList([
            'offset' => 0 < ($offset = $request->query->get('offset', 0)) ? 
                $offset : 0,
            'limit' => 32 < ($limit = $request->query->get('limit', 32)) ? 
                $limit : 32
        ]);

        /**
         * @var string $html
         * Builds the view raw html.
         */
        $html = $this->template->render('admin/storage.html.twig', [
            'title' => 'Vector - Storage',
            'description' => 'Storage administration area.',
            'assets' => $assets
        ]);

        return new Response($html, Response::HTTP_OK);
    }

    /**
     * Route: '/admin/storage/upload'
     * Methods: POST 
     * @param Request $request
     * @return RedirectResponse
     */
    public function storageUploadAction(Request $request): RedirectResponse
    {

        /**
         * @var array $files
         * Uploaded files are retrived from the request object.
         * No file contraints are applied.
         */
        $files = $request->files->get('files');
        if (!is_array($files) || empty($files)) {
            return new RedirectResponse(
                '/admin/storage?success=false', 
                Response::HTTP_FOUND
            );
        }

        /**
         * @var Asset $asset
         * Asset object instances are created to handle database updates. 
         */
        foreach ($files as $file) {
            $asset = new Asset([
                'path' => $file->getClientOriginalName(),
                'mimeType' => $file->getMimeType(),
                'size' => $file->getSize(),
                'content' => $file->getContent()
            ]);
            $asset->save();
        }

        return new RedirectResponse(
            '/admin/storage?success=true', 
            Response::HTTP_FOUND
        );
    }

}
