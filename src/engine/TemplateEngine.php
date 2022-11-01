<?php
namespace Vector\Engine;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class TemplateEngine {

    private $template;

    /**
     * @package Vector
     * __construct()
	 * @param {string} $template
     */
    public function __construct(string $template) {
        $this->template = $template;
    }

    /**
	 * @package Vector
	 * Vector\Engine\TemplateEngine->parse()
	 */
    public function parse(): string {
        ob_start();
        require_once(__DIR__ . '/../template-parts/header.php');
        require_once(__DIR__ . "/../templates/{$this->template}.php");
        require_once(__DIR__ . '/../template-parts/footer.php');
        return ob_get_clean();
    }

}