<?php

namespace Vector\Controller\Admin;

use Vector\Router;
use Vector\DataObject\Asset;
use Vector\Module\Controller\FrontendController;
use Vector\Repository\AssetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class StorageController extends FrontendController
{

    protected const ITEMS_PER_PAGE = 32;

    protected function register(): void
    {
        Router::route(['GET'], '^/admin/storage?$', [$this, 'storageViewAction']);
        Router::route(['POST'], '^/admin/storage/upload?$', [$this, 'storageUploadAction']);
        Router::route(['POST'], '^/admin/storage/delete?$', [$this, 'storageDeleteAction']);
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
         * @var int $totalCount
         * @var int $page
         * The page parameter is retrived from the current request.
         * If invalid values are passed page will default to 0 or max.
         */
        $repository = AssetRepository::getInstance();
        $totalCount = $repository->getTotalCount();
        $page = intval((0 >= ($page = $request->get('page', 1))) ? 1 : (int) $page);
        if ($page > ($pageCount = ceil($totalCount / self::ITEMS_PER_PAGE))) {
            $page = intval($pageCount);
        }

        /**
         * @var AssetRepository $repository
         * @var ?array<Asset> $assets 
         * Stored assets are retrived.
         */
        $repository = AssetRepository::getInstance();
        $assets = $repository->getList(
            '1 = 1', 
            self::ITEMS_PER_PAGE, 
            $page <= 1 ? 0 : ($page - 1) * self::ITEMS_PER_PAGE
        );

        /**
         * @var string $html
         * Builds the view raw html.
         */
        $html = $this->template->render('admin/storage.html.twig', [
            'title' => 'Vector - Storage',
            'description' => 'Storage administration area.',
            'assets' => $assets,
            'currentPage' => $page,
            'prevPage' => ($page > 1) ? ($page - 1) : 1,
            'nextPage' => ($page < $pageCount) ? ($page + 1) : $pageCount
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
                '/admin/storage', 
                Response::HTTP_FOUND
            );
        }

        /**
         * @var Asset $asset
         * @var UploadedFile $file
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
            '/admin/storage', 
            Response::HTTP_FOUND
        );
    }

    /**
     * Route: '/admin/storage/delete'
     * Methods: POST 
     * @param Request $request
     * @param array $params
     * @return RedirectResponse
     */
    public function storageDeleteAction(Request $request): RedirectResponse
    {

        /**
         * @var int $media
         * @var AssetRepository $repository
         * @var Asset|null $asset
         * The retrived media is deleted.
         */
        if (null !== ($media = $request->request->get('media', null))) {
            $repository = AssetRepository::getInstance();
            if (null !== ($asset = $repository->getById($media))) {
                $asset->delete();
                return new RedirectResponse(
                    '/admin/storage', 
                    Response::HTTP_FOUND
                );
            }
        }

        return new RedirectResponse(
            '/admin/storage', 
            Response::HTTP_FOUND
        );
    }

}
