<?php

namespace Vector\Module;

use Vector\Module\ApplicationLogger\FileSystemLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * Vector\Module\ErrorHandler->handleError()
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return void
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {

        /**
         * @var object $config
         * @var string $exceptionMessage
         * Load global configuration.
         * If debug is enabled $errorMessage is logged, displayed or both.
         */
        global $config;
        $errorMessage = 'Error: "' . $errstr . '" in "' . $errfile . '" at line "' . $errline . '"';
        if (true === $config->debug) {
            $this->outputErrorBox($errorMessage);
        }
        if (true === $config->debug_log) {
            $this->logger->write($errorMessage);
        }

        die();
    }

    /**
     * @package Vector
     * Vector\Module\ErrorHandler->handleException()
     * @param mixed $exception
     * @return void
     */
    public function handleException(mixed $exception): void
    {

        /**
         * @var object $config
         * @var string $exceptionMessage
         * Load global configuration.
         * If debug is enabled $exceptionMessage is logged, displayed or both.
         */
        global $config;
        $exceptionMessage = 'Exception: "' . $exception->getMessage() . '" in "' . $exception->getFile() . '" at line "' . $exception->getLine() . '"';
        if (true === $config->debug) {
            $this->outputErrorBox($exceptionMessage);
        }
        if (true === $config->debug_log) {
            $this->logger->write($exceptionMessage);
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

        /**
         * @var object $config
         * @var string $exceptionMessage
         * Load global configuration.
         * If debug is enabled $errorMessage is logged, displayed or both.
         */
        global $config;
        $lastError = error_get_last();
        if ($lastError !== null && in_array($lastError['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $errorMessage = 'Fatal error: "' . $lastError['message'] . '" in "' . $lastError['file'] . '" at line "' . $lastError['line'] . '"';
            if (true === $config->debug) {
                $this->outputErrorBox($errorMessage);
            }
            if (true === $config->debug_log) {
                $this->logger->write($errorMessage);
            }

            /**
             * @var Request $request
             * Load global $request, responde and die.
             */
            global $request;
            $response = new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->prepare($request);
            $response->send();

        }

        die();
    }

    /**
     * @package Vector
     * Vector\Module\ErrorHandler->outputErrorBox()
     * @param string $message
     * @return void
     */
    protected function outputErrorBox(string $message): void
    {
        ob_start(); ?>
            <div style="padding: 25px;margin: 5px auto;background-color: #fff;border: 2px solid #af0000;position: relative;z-index: 1000;">
                <?php echo $message; ?>
            </div>
        <?php echo ob_get_clean();
    }

}
