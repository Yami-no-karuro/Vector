<?php
namespace Vector\Engine;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Template {

    private string $template;
    private static mixed $template_data;

    /**
     * @package Vector
     * __construct()
	 * @param {string} $template
     */
    public function __construct(string $template, mixed $data) {
        $this->template = $template;
        self::$template_data = $data;
    }

    /**
	 * @package Vector
	 * Vector\Engine\Template->parse()
     * @param {mixed} $data
     * @return string
	 */
    public function parse(): string {
        ob_start();
        require_once(__DIR__ . "/../templates/{$this->template}");
        return ob_get_clean();
    }

    /**
	 * @package Vector
	 * Vector\Engine\Template::get_template_part()
     * @return void
	 */
    public static function get_template_part(string $template_part): void {
        require_once(__DIR__ . "/../template-parts/{$template_part}");
    }

    /**
	 * @package Vector
	 * Vector\Engine\Template::get_script_tag()
     * @param {string} $source
     * @param {bool} $defer
     * @param {bool} $async
     * @return void
	 */
    public static function get_script_tag(string $source, bool $defer = false, bool $async = false): void {
        $defer = $defer ? 'defer' : '';
        $async = $async ? 'async' : '';
        echo "<script type='text/javascript' src='" . APP_URL . "assets/{$source}' {$defer} {$async}></script> \n";
    }

    /**
	 * @package Vector
	 * Vector\Engine\Template::get_style_tag()
     * @param {string} $source
     * @return void
	 */
    public static function get_style_tag(string $source): void {
        echo "<link rel='stylesheet' href='" . APP_URL . "assets/{$source}'> \n";
    }

}