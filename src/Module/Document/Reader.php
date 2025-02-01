<?php

namespace App\Module\CSV;

use Generator;

class Reader
{

    protected mixed $handle;
    protected string $delimiter;

    /**
     * @package Vector
     * @param mixed $file
     * @param string $delimeter
     */
    public function __construct(mixed $file, string $delimiter = ',')
    {
        $this->handle = $file;
        if (!is_resource($file))
            $this->handle = fopen($file, 'r');

        $this->delimiter = $delimiter;
    }

    /**
     * @package Vector
     * __construct()
     */
    public function __destruct()
    {
        if (is_resource($this->handle))
            fclose($this->handle);
    }

    /** 
     * @package Vector
     * @return Generator 
     */
    public function getRows(): Generator
    {
        while (!feof($this->handle)) {
            $row = fgetcsv($this->handle, 0, $this->delimiter);
            if ($row !== false)
                yield $row;
        }
    }
}
