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
        $this->sql = SqlConnection::getInstance();
        if (true === DATABASE_LOGS) {
            $this->applicationLogger = new SqlLogger('controller');
        } else { $this->applicationLogger = new FileSystemLogger('controller'); }
        $this->template = new Environment(new FilesystemLoader(__DIR__ . '/../templates'), [
            'cache'       => __DIR__ . '/../var/cache/twig',
            'auto_reload' => true
        ]);
        if (!$direct) {
            $this->router = Router::getInstance($request, $path); 
            $this->init(); 
        }
    }

    
    /**
     * @package Vector
     * Vector\Module\AbstractController->init
     * @return void
     */
    abstract protected function init(): void;

}