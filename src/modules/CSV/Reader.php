<?php

namespace Vector\Module\CSV;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Reader
{
    protected mixed $fileHandle;
    protected string $delimiter;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(string $filename, string $delimiter = ',')
    {
        $this->fileHandle = fopen($filename, 'r');
        $this->delimiter = $delimiter;
    }

    /**
     * @package Vector
     * __destruct()
     */
    public function __destruct()
    {
        fclose($this->fileHandle);
    }

    /**
     * @package Vector
     * Vector\Module\CSV\Reader->getRow()
     * @return false|array
     */
    public function getRow(): false|array
    {
        if (!feof($this->fileHandle)) {
            return fgetcsv($this->fileHandle, 0, $this->delimiter);
        }
        return false;
    }

}
