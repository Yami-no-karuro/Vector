<?php
namespace Vector\Engine;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class TemplateEngine {

    public $template;

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
        require_once(__DIR__ . "/../templates/{$this->template}.php");
        return ob_get_clean();
    }

    /**
	 * @package Vector
	 * Vector\Engine\TemplateEngine->get_template_part()
	 */
    public static function get_template_part(string $template_part): void {
        require_once(__DIR__ . "/../template-parts/{$template_part}.php");
    }

}