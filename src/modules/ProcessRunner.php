<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class ProcessRunner
{
    protected $process;
    private static mixed $instance = null;

    /**
     * @package Vector
     * Vector\Module\ProcessRunner::getInstance()
     * @return ProcessRunner
     */
    public static function getInstance(): ProcessRunner
    {
        if (self::$instance == null) {
            self::$instance = new ProcessRunner();
        }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Module\ProcessRunner->runCommand()
     * @param string $command
     * @return ?array
     */
    public function runCommand(string $command): ?array
    {
        $this->process = proc_open($command, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ], $pipes);
        if (is_resource($this->process)) {
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $returnValue = proc_close($this->process);
            return [
                'output' => $output,
                'error' => $error,
                'return_value' => $returnValue
            ];
        }
        return null;
    }

}
