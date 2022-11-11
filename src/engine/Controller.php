<?php
namespace Vector\Engine;
use Vector\Router;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class Controller {

    /**
     * @package Vector
     * __construct()
     */
    protected Router $router;
    public function __construct() {
        $this->router = Router::get_instance();
        $this->init();
    }

    
    /**
     * @package Vector
     * Vector\Engine\Controller->init
     * @return void
     */
    abstract protected function init();

}