<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Process {

    protected string $command;
    protected array $descriptorspec;

    /** @param array $commandArr */
    public function __construct($commandArr)
    {
        $this->command = implode(' ', $commandArr);
        $this->descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['file', __DIR__ . '/../var/log/process.log.txt', 'a']
        ];
    }

    /** @return int|false */
    public function execute(): int|false
    {
        $process = @proc_open($this->command, $this->descriptorspec, $pipes);
        if (is_resource($process)) {
            array_map(function($pipe) {
                fclose($pipe);
            }, $pipes);
            return proc_close($process);
        }
        return false;
    }

}