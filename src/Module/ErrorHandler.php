<?php

namespace Vector\Module;

use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\ApplicationLogger\SqlLogger;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class ErrorHandler
{

    protected FileSystemLogger $filesystemLogger;
    protected SqlLogger $sqlLogger;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->filesystemLogger = new FileSystemLogger('error');
        $this->sqlLogger = new SqlLogger('error');
    }

    /**
     * @package Vector
     * Vector\Module\ErrorHandler->handleError()
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return void
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        global $config;
        $message = "Error: \"{$errstr}\" in {$errfile} at line {$errline}";

        if ($config->debug_log) {
            $this->filesystemLogger->write($message);
            $this->sqlLogger->write($message);
        }

        if ($config->debug) {
            echo $message;
        }

        die();
    }

    /**
     * @package Vector
     * Vector\Module\ErrorHandler->handleException()
     * @param mixed $e
     * @return void
     */
    public function handleException(mixed $e): void
    {
        global $config;
        $message = "Exception: \"{$e->getMessage()}\" in {$e->getFile()} at line {$e->getLine()}";

        if ($config->debug_log) {
            $this->filesystemLogger->write($message);
            $this->sqlLogger->write($message);
        }

        if ($config->debug) {
            echo $message;
        }

        die();
    }

    /**
     * @package Vector
     * Vector\Module\ErrorHandler->handleShutdown()
     * @return void
     */
    public function handleShutdown(): void
    {
        global $config;
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $message = "Fatal error: \"{$error['message']}\" in {$error['file']} at line {$error['line']}";

            if ($config->debug_log) {
                $this->filesystemLogger->write($message);
                $this->sqlLogger->write($message);
            }

            if ($config->debug) {
                echo $message;
            }
        }

        die();
    }

}
