<?php

namespace Vector\Module;

use Vector\Router;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\ApplicationLogger\SqlLogger;
use Vector\Module\SqlConnection;
use Symfony\Component\HttpFoundation\Request;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class AbstractController {

    protected Router $router;
    protected SqlConnection $sql;
    protected FileSystemLogger|SqlLogger $applicationLogger;
    protected Environment $template;

    /**
     * @package Vector
     * @param Request $request
     * @param string $path
     * @param bool $direct
     * __construct()
     */
    public function __construct(Request $request = null, string $path, bool $direct = false) 
    {

        /** @var SqlConnection $sql */
        $this->sql = SqlConnection::getInstance();
        
        /** @var SqlLogger|FileSystemLogger $applicationLogger */
        if (true === DATABASE_LOGS) {
            $this->applicationLogger = new SqlLogger('controller');
        } else { $this->applicationLogger = new FileSystemLogger('controller'); }
        
        /**
         * @var Envoirment $template
         * @var FilesystemLoader
         */
        $filesystemLoader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->template = new Environment($filesystemLoader, [
            'cache'       => __DIR__ . '/../../var/cache/twig',
            'auto_reload' => true
        ]);

        /** If called directly Controller doest not have to register Routes */
        if (!$direct) { $this->init($request, $path); }

    }

    
    /**
     * @package Vector
     * Vector\Module\AbstractController->init
     * @param Request $request
     * @param string $path
     * @return void
     */
    abstract protected function init(Request $request, string $path): void;

}