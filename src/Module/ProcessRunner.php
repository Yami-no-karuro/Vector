<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class ProcessRunner
{
    /**
     * @package Vector
     * Vector\Module\ProcessRunner::run()
     * @param string $command
     * @return ?array
     */
    public static function run(string $command): ?array
    {
        $process = proc_open($command, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ], $pipes);

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $code = proc_close($process);
            return [
                'output' => $output,
                'error' => $error,
                'code' => $code
            ];
        }

        return null;
    }

}
