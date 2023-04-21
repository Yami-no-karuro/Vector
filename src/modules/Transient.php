<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Transient {

    protected string $filepath;
    public mixed $content;
    public mixed $lsmTime;

    /**
     * @package Vector
     * @param string $transient
     * __construct()
     */
    public function __construct(string $transient) 
    {
        $this->filepath = __DIR__ . '/../var/cache/transients/' . md5($transient);
        $this->content = @file_get_contents($this->filepath, true);
        $this->lsmTime = @filemtime($this->filepath);
    }

    /**
     * @package Vector
     * Vector\Module\Transient->isValid() 
     * @param int $seconds
     * @return bool
     */
    public function isValid(int $seconds): bool
    {
        return (time() - $this->lsmTime) > $seconds ? false : true;
    }

    /**
     * @package Vector
     * Vector\Module\Transient->getContent()
     * @return mixed
     */
    public function getContent(): mixed 
    {
        return $this->content;
    }

    /**
     * @package Vector
     * Vector\Module\Transient->setContent()
	 * @param mixed $data
     * @return bool
     */
    public function setContent(mixed $data): bool 
    {
        return @file_put_contents($this->filepath, $data);
    }

    /**
     * @package Vector
     * Vector\Module\Transitne->delete()
     * @return bool
     */
    public function delete(): bool
    {
        return @unlink($this->filepath);
    }

}