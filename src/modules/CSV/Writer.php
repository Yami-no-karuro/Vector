<?php

namespace Vector\Module\CSV;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Writer
{
    protected mixed $fileHandle;
    protected string $delimiter;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(string $filename, string $delimiter = ',')
    {
        $this->fileHandle = fopen($filename, 'w');
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
     * Vector\Module\CSV\Writer->writeRow()
     * @return void
     */
    public function writeRow($data): void
    {
        fputcsv($this->fileHandle, $data, $this->delimiter);
    }

    /**
     * @package Vector
     * Vector\Module\CSV\Writer->writeRows()
     * @return void
     */
    public function writeRows($data): void
    {
        foreach ($data as $row) {
            fputcsv($this->fileHandle, $row, $this->delimiter);
        }
    }

}
