<?php

namespace Vector\Module;

use Vector\Router;
use Vector\Module\ApplicationLogger;
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
    protected ApplicationLogger $applicationLogger;
    protected Environment $template;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(Request $request = null) 
    {
        $this->router = Router::getInstance($request);
        $this->sql = SqlConnection::getInstance();
        $this->applicationLogger = new ApplicationLogger('controllers');
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $this->template = new Environment($loader, [
            'cache'       => __DIR__ . '/../var/cache/twig',
            'auto_reload' => true
        ]);
        $this->init();
    }

    
    /**
     * @package Vector
     * Vector\Module\AbstractController->init
     * @return void
     */
    abstract protected function init(): void;

}