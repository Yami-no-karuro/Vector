<?php
namespace Vector\Engine;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Transient {

    private string $filepath;
    public mixed $content;
    public mixed $lsm_time;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct($transient) {
        $this->filepath = __DIR__ . '/../var/transients/' . md5($transient);         
        $this->content = @file_get_contents($this->filepath, true);
        $this->lsm_time = @filemtime($this->filepath);
    }

    /**
     * @package Vector
     * Vector\Engine\Transient::get_data()
	 * @param {int} $seconds
     * @return mixed
     */
    public function get_data(int $seconds): mixed {
        return (object) [
            'valid'   => (time() - $this->lsm_time) > $seconds ? false : true,
            'content' => $this->content
        ];
    }

    /**
     * @package Vector
     * Vector\Engine\Transient::set_data()
	 * @param {mixed} $data
     * @return bool
     */
    public function set_data(mixed $data): bool {
        return @file_put_contents($this->filepath, $data);
    }

}