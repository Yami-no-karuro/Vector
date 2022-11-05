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
     * @param {mixed} $data
     */
    public function __construct(string $template, mixed $data = NULL) {
        $this->template = $template;
        global $template_data;
        $template_data = $data;
    }

    /**
     * @package Vector
     * __destruct()
     */
    public function __destruct() {
        global $template_data;
        unset($template_data);
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
	 * Vector\Engine\TemplateEngine::get_template_part()
	 */
    public static function get_template_part(string $template_part): void {
        require_once(__DIR__ . "/../template-parts/{$template_part}.php");
    }

    /**
	 * @package Vector
	 * Vector\Engine\TemplateEngine::get_script_tag()
     * @param {string} $source
     * @param {bool} $defer
     * @param {bool} $async
	 */
    public static function get_script_tag(string $source, bool $defer = false, bool $async = false): void {
        $defer = $defer ? 'defer' : '';
        $async = $async ? 'async' : '';
        echo "<script type='text/javascript' src='" . APP_URL . "assets/{$source}' {$defer} {$async}></script> \n";
    }

    /**
	 * @package Vector
	 * Vector\Engine\TemplateEngine::get_style_tag()
     * @param {string} $source
	 */
    public static function get_style_tag(string $source): void {
        echo "<link rel='stylesheet' href='" . APP_URL . "assets/{$source}'> \n";
    }

}