<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class External {

    protected string $resource;
    protected string $initiator;
    protected array $descriptorspec;

    /** @param string $process */
    public function __construct(string $process, string $initiator)
    {
        $this->resource = __DIR__ . '/../external/' . $process;
        $this->initiator = $initiator;
        $this->descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['file', __DIR__ . '/../var/log/proc_open.log.txt', 'a']
        ];
    }

    /** @return int */
    public function execute(): int
    {
        $process = proc_open($this->initiator . ' ' . $this->resource, $this->descriptorspec, $pipes);
        if (is_resource($process)) {
            array_map('fclose', $pipes);
            return proc_close($process);
        }
    }

}