<?php

namespace Vector\Module;

use Vector\Router;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class AbstractController {

    protected Router $router;
    protected Environment $template;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct() 
    {
        $this->router = Router::getInstance();
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $this->template = new Environment($loader, [
            'cache'       => __DIR__ . '/../var/cache',
            'auto_reload' => true
        ]);
        $this->init();
    }

    
    /**
     * @package Vector
     * Vector\Module\AbstractController->init
     * @return void
     */
    abstract protected function init();

}