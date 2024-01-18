<?php

namespace Vector\Controller;

use Vector\Kernel;
use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\Storage\S3StorageAdapter;
use Vector\Repository\AssetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Exception;

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
         * @var ?array $assets 
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
         * @var FileSystemLogger $logger
         * @var array $files
         * Uploaded files are retrived from the request object.
         * No file contraints are applied.
         */
        $logger = new FileSystemLogger('controller');
        $files = $request->files->get('files');
        if (!is_array($files) || empty($files)) {
            return new RedirectResponse(
                '/admin/storage?success=false', 
                Response::HTTP_FOUND
            );
        }

        /**
        * @var S3StorageAdapter $adapter
        * @var FileSystem $filesystem
        * If the remote storage is enabled the filesystem component is initialized.
        */
        global $config;
        if ($config->s3_storage->enabled === true) {
            $adapter = S3StorageAdapter::getInstance();
            $filesystem = $adapter->getFileSystemComponent();
        }

        /**
         * @var AssetRepository $repository
         * The asset repository instance is retrived to handle database updates.
         */
        $repository = AssetRepository::getInstance();
        foreach ($files as $file) {
            $repository->save([
                'path' => $file->getClientOriginalName(),
                'mimetype' => $file->getMimeType(),
                'size' => $file->getSize(),
                'modified_at' => time()
            ]);

            /**
             * @var string $filepath
             * If the remote storage is enabled the file is directly uploaded to the bucket.
             * Local storage is used otherwise.
             */
            try {
                if ($config->s3_storage->enabled === true) {
                    $filesystem->write('/' . $file->getClientOriginalName(), $file->getContent());
                } else { 
                    file_put_contents(
                        Kernel::getProjectRoot() . 'var/storage/' . $file->getClientOriginalName(), 
                        $file->getContent()
                    ); 
                }
            } catch (Exception $e) {
                $logger->write($e);
                return new RedirectResponse(
                    '/admin/storage?success=false', 
                    Response::HTTP_FOUND
                );
            }
        }

        return new RedirectResponse(
            '/admin/storage?success=true', 
            Response::HTTP_FOUND
        );
    }

}
