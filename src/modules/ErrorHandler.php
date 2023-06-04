<?php

namespace Vector\Module;

use Vector\Module\ApplicationLogger\FileSystemLogger;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class ErrorHandler
{
    protected FileSystemLogger $logger;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct()
    {
        $this->logger = new FileSystemLogger('core');
    }

    /**
     * @package Vector
     * Vector\Module\ErrorHandler::handleError()
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return void
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        global $config;
        if (true === $config->debug_log) {
            $errorMessage = 'Error: "' . $errstr . '" in "' . $errfile . '" at line "' . $errline . '"';
            $this->logger->write($errorMessage);
        }
    }

    /**
     * @package Vector
     * Vector\Module\ErrorHandler::handleException()
     * @param mixed $exception
     * @return void
     */
    public function handleException(mixed $exception): void
    {
        global $config;
        if (true === $config->debug_log) {
            $exceptionMessage = 'Exception: "' . $exception->getMessage() . '" in "' . $exception->getFile() . '" at line "' . $exception->getLine() . '"';
            $this->logger->write($exceptionMessage);
        }
    }

    /**
     * @package Vector
     * Vector\Module\ErrorHandler::handleShutdown()
     * @return void
     */
    public function handleShutdown(): void
    {
        global $config;
        if (true === $config->debug_log) {
            $lastError = error_get_last();
            if ($lastError !== null && in_array($lastError['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                $errorMessage = 'Fatal error: "' . $lastError['message'] . '" in "' . $lastError['file'] . '" at line "' . $lastError['line'] . '"';
                $this->logger->write($errorMessage);
                die();
            }
        }
    }

}
