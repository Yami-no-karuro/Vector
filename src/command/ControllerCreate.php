<?php

namespace Vector\Command;

use Vector\Kernel;
use Vector\Module\Console\AbstractCommand;
use Vector\Module\Console\Application;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class ControllerCreate extends AbstractCommand
{
    /**
     * @package Vector
     * Vector\Command\ControllerCreate->execute()
     * @return int
     */
    public function execute(): int
    {

        /**
         * @var array $args
         * Check the provided arguments.
         * "controller name" and "controller type" are required.
         */
        if (null === ($args = $this->getArgs()) or empty($args)) {
            Application::out('Invalid arguments, you need to provide controller name and type.');
            Application::out('Chose controller type from: "frontend", "rest".');
            return self::EXIT_FAILURE;
        }

        /**
         * @var string $controllerName
         * @var string $controllerType
         * @var string $templatePath
         * List the provided arguments.
         * "controller type" argument will be used to construct the template path.
         * Defaults to "fr_controller_template.txt".
         */
        list($controllerName, $controllerType) = $args;
        $templatePath = match ($controllerType) {
            'frontend' => Kernel::getProjectRoot() . 'var/source/fr_controller_template.txt',
            'rest' => Kernel::getProjectRoot() . 'var/source/rs_controller_template.txt',
            default => Kernel::getProjectRoot() . 'var/source/fr_controller_template.txt'
        };

        /**
         * @var string $fileContent
         * @var string $filePath
         * Build controller content from template.
         */
        $filePath = Kernel::getProjectRoot() . 'src/controller/' . $controllerName . '.php';
        if (file_exists($filePath)) {
            Application::out('Cannot create controller "' . $controllerName .  '", file already exits!');
            return self::EXIT_FAILURE;
        }
        $fileContent = str_replace('%controller_name%', $controllerName, file_get_contents($templatePath));
        file_put_contents($filePath, $fileContent);

        Application::out('Controller "' . $controllerName .  '" successfully created!');
        return self::EXIT_SUCCESS;
    }

    /**
     * @package Vector
     * Vector\Command\ControllerCreate->getCommandName()
     * @return void
     */
    public function getCommandName(): string
    {
        return 'vector:controller-create';
    }

}
