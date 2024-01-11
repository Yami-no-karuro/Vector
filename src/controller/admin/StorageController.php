<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Vector\Module\Storage\S3StorageAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Vector\Kernel;
use Vector\Repository\AssetRepository;

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
     * @return Response
     */
    public function storageViewAction(): Response
    {

        /**
         * @var string $html
         * Builds the view raw html.
         */
        $html = $this->template->render('admin/storage.html.twig', [
            'title' => 'Vector - Storage',
            'description' => 'Storage administration area.'
        ]);

        return new Response($html, Response::HTTP_OK);
    }

    /**
     * Route: '/admin/storage/upload'
     * Methods: POST 
     * @param Request $request
     * @return Response
     */
    public function storageUploadAction(Request $request): Response
    {

        /**
         * @var S3StorageAdapter $adapter
         * @var FileSystem $filesystem
         * The S3 filesystem component is initialized.
         */
        global $config;
        if ($config->s3_storage->enabled === true) {
            $adapter = S3StorageAdapter::getInstance();
            $filesystem = $adapter->getFileSystemComponent();
        }

        /**
         * @var array $files
         * Uploaded files are retrived from the request object.
         */
        $files = $request->files->get('files');
        if (is_array($files) && !empty($files)) {

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
                 * If the remote storage is configured the file is directly uploaded.
                 */
                if ($config->s3_storage->enabled === true) {
                    $filesystem->write($file->getClientOriginalName(), $file->getContent());
                } else { 
                    $filepath = Kernel::getProjectRoot() . 'var/storage/' . $file->getClientOriginalName(); 
                    file_put_contents($filepath, $file->getContent()); 
                }
            }
        }

        return new RedirectResponse('/admin/storage', Response::HTTP_FOUND);
    }

}
