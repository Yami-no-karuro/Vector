<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class SubProcess {

    protected string $resource;
    protected array $descriptorspec;

    /** @param string $processName */
    public function __construct(string $processName)
    {
        $this->resource = __DIR__ . '/../subs/' . $processName . '.php';
        $this->descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['file', __DIR__ . '/../var/log/subprocs.log.txt', 'a']
        ];
    }

    /** @return int */
    public function execute(): int
    {
        $process = proc_open('php ' . $this->resource, $this->descriptorspec, $pipes);
        if (is_resource($process)) {
            array_map('fclose', $pipes);
            return proc_close($process);
        }
    }

}