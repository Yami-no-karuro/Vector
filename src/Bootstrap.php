<?php

namespace Vector;

use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Bootstrap {

    protected \RecursiveDirectoryIterator $dir;
    protected \RecursiveIteratorIterator $iterator;
    protected Request $request;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->dir = new \RecursiveDirectoryIterator(__DIR__ . '/../src/controllers');
        $this->iterator = new \RecursiveIteratorIterator($this->dir);
        $this->request = Request::createFromGlobals();
    }


    /**
     * @package Vector
     * Vector\Bootstrap->boot()
     * @return void
     */
    public function boot(): void
    {
        foreach ($this->iterator as $file) {
            $fname = $file->getFilename();
            if (preg_match('%\.php$%', $fname)) { 
                require_once ($file->getPathname());
                $controller = 'Vector\\Controller\\' . basename($fname, '.php'); 
                new $controller($this->request); 
            }
        }
    }
    
}