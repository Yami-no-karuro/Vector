<?php

namespace Vector\Module;

use Vector\Module\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

abstract class FrontendController extends AbstractController {

    protected Request $request;
    protected Environment $template;

    /**
     * @package Vector
     * __construct()
     * @param bool $direct
     */
    public function __construct(bool $direct = false) 
    {
        /**
         * @var Envoirment $template
         * @var FilesystemLoader
         */
        $filesystemLoader = new FilesystemLoader(__DIR__ . '/../../../templates');
        $this->template = new Environment($filesystemLoader, [
            'cache'       => __DIR__ . '/../../../var/cache/twig',
            'auto_reload' => true
        ]);

        parent::__construct($direct);

    }

}