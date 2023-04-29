<?php

namespace Vector;

use Vector\Module\SqlConnection;
use Symfony\Component\HttpFoundation\Request;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Kernel {

    protected Request $request;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    /**
     * @package Vector
     * Vector\Bootstrap->boot()
     * @return void
     */
    public function boot(): void
    {
        $path = parse_url($this->request->getRequestUri())['path'];
        if (true === DATABASE_ROUTES) {
            $sql = SqlConnection::getInstance();
            $cache = $sql->getResults("SELECT `path`, `controller` FROM `routes` WHERE `path` = ? LIMIT 1", [
                ['type' => 's', 'value' => $path]
            ]);
            if ($cache['success'] AND !empty($cache['data'])) {
                $cacheData = $cache['data'];
                if ($cacheData['path'] === $path) {
                    $controller = new $cacheData['controller']($this->request);
                }
            }
        } else {
            $cacheFile = __DIR__ . '/var/cache/router/' . md5($path);
            if (file_exists($cacheFile)) {
                $cacheData = unserialize(@file_get_contents($cacheFile));
                if ($cacheData['path'] === $path) {
                    $controller = new $cacheData['controller']($this->request);
                }
            }
        }
        $dir = new RecursiveDirectoryIterator(__DIR__ . '/../src/controllers');
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            $fname = $file->getFilename();
            if (preg_match('%\.php$%', $fname)) { 
                require_once ($file->getPathname());
                $controller = 'Vector\\Controller\\' . basename($fname, '.php'); 
                new $controller($this->request); 
            }
        }
    }

    /**
     * @package Vector
     * Vector\Bootstrap->loadConfig()
     * @return void
     */
    public function loadGlobals(): void 
    {
        global $params;
        $params = [
            'foo' => 'bar',
            'bar' => 'foo'
        ];
    }
    
}