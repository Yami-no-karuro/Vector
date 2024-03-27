<?php

namespace Vector\Controller\Admin\Api;

use Vector\Router;
use Vector\Module\Controller\RestController;
use Vector\DataObject\Asset;
use Vector\Repository\AssetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class StorageController extends RestController
{

    protected const ITEMS_PER_PAGE = 32;

    protected function register(): void
    {
        Router::route(['GET'], '^/admin/api/storage/?$', [$this, 'storageListingAction']);
        Router::route(['POST'], '^/admin/api/storage/?$', [$this, 'storageUploadAction']);
        Router::route(['DELETE'], '^/admin/api/storage/(?<id>\d+)$', [$this, 'storageDeleteAction']);
    }

    /**
     * Route: '/admin/api/storage'
     * Methods: GET
     * @param Request $request
     * @return JsonResponse
     */
    public function storageListingAction(Request $request): JsonResponse
    {
        $repository = AssetRepository::getInstance();
        $totalCount = $repository->getTotalCount();
        $page = intval((0 >= ($page = $request->get('page', 1))) ? 1 : (int) $page);
        if ($page > ($pageCount = ceil($totalCount / self::ITEMS_PER_PAGE))) {
            $page = intval($pageCount);
        }

        $assets = $repository->getList([
            'limit' => self::ITEMS_PER_PAGE,
            'offset' => $page <= 1 ? 0 : ($page - 1) * self::ITEMS_PER_PAGE
        ]);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'currentPage' => $page,
                'prevPage' => ($page > 1) ? ($page - 1) : 1,
                'nextPage' => ($page < $pageCount) ? ($page + 1) : $pageCount,
                'list' => array_map(function($el) {
                    return [
                        'ID' => $el->getId(),
                        'path' => $el->getPath(),
                        'route' => $el->getRoute(),
                        'mimeType' => $el->getMimeType(),
                        'size' => $el->getSize(),
                        'system' => [
                            'createdAt' => date("d-m-Y H:i:s", $el->getCreatedAt()),
                            'modifiedAt' => date("d-m-Y H:i:s", $el->getModifiedAt())                     
                        ]
                    ];
                }, $assets)
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Route: '/admin/api/storage'
     * Methods: POST
     * @param Request $request
     * @return JsonResponse
     */
    public function storageUploadAction(Request $request): JsonResponse
    {
        $files = $request->files->get('files');
        if (!is_array($files) || empty($files)) {
            return new JsonResponse([
                'success' => false,
                'data' => null    
            ], Response::HTTP_BAD_REQUEST);
        }

        $assets = [];
        foreach ($files as $file) {
            $asset = new Asset();
            $asset->setPath($file->getClientOriginalName());
            $asset->setMimeType($file->getMimeType());
            $asset->setSize($file->getSize());
            $asset->setContent($file->getContent());

            $asset->save();
            $asset[] = $asset;
        }

        return new JsonResponse([
            'success' => true,
            'data' => array_map(function($el) {
                return [
                    'ID' => $el->getId(),
                    'path' => $el->getPath(),
                    'route' => $el->getRoute(),
                    'mimeType' => $el->getMimeType(),
                    'size' => $el->getSize(),
                    'system' => [
                        'createdAt' => date("d-m-Y H:i:s", $el->getCreatedAt()),
                        'modifiedAt' => date("d-m-Y H:i:s", $el->getModifiedAt())                     
                    ]
                ];
            }, $assets)
        ], Response::HTTP_CREATED);
    }

    /**
     * Route: '/admin/api/storage/<id>'
     * Methods: DELETE 
     * @param Request $request
     * @param array $params
     * @return JsonResponse
     */
    public function storageDeleteAction(Request $request, array $params): JsonResponse
    {
        $repository = AssetRepository::getInstance();
        if (null !== ($asset = $repository->getById($params['id']))) {
            $asset->delete();

            return new JsonResponse([
                'success' => true,
                'data' => null 
            ], Response::HTTP_OK);
        }

        return new JsonResponse([
            'success' => false,
            'data' => null 
        ], Response::HTTP_NOT_FOUND);
    }
}