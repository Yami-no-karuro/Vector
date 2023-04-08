<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Transient {

    private string $filepath;
    public mixed $content;
    public mixed $lsmTime;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct($transient) 
    {
        $this->filepath = __DIR__ . '/../var/transients/' . md5($transient) . '.txt';         
        $this->content = file_get_contents($this->filepath, true);
        $this->lsmTime = filemtime($this->filepath);
    }

    /**
     * @package Vector
     * Vector\Module\Transient->getData()
	 * @param int $seconds
     * @return object
     */
    public function getData(int $seconds): array 
    {
        return [
            'valid'   => (time() - $this->lsmTime) > $seconds ? false : true,
            'content' => $this->content
        ];
    }

    /**
     * @package Vector
     * Vector\Module\Transient->setData()
	 * @param mixed $data
     * @return bool
     */
    public function setData(mixed $data): bool 
    {
        return file_put_contents($this->filepath, $data);
    }

    /**
     * @package Vector
     * Vector\Module\Transitne->delete()
     * @return bool
     */
    public function delete(): bool
    {
        return unlink($this->filepath);
    }

}