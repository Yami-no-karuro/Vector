<?php
namespace Vector\Functions;
use Vector\Router;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    die(); 
}

abstract class Controller {

    /**
     * @package Vector
     * __construct()
     */
    protected $router;
    public function __construct() {
        $this->router = Router::get_instance();
        $this->init();
    }

    
    /**
     * @package Vector
     * Vector\Functions\Controller->init
     */
    abstract protected function init();

}