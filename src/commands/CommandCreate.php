<?php

namespace Vector\Command;

use Vector\Kernel;
use Vector\Module\Console\AbstractCommand;
use Vector\Module\Console\Application;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class CommandCreate extends AbstractCommand
{
    /**
     * @package Vector
     * Vector\Command\CommandCreate->execute()
     * @return int
     */
    public function execute(): int
    {

        /**
         * @var array $args
         * Check the provided arguments.
         * "command class" and "command" are required.
         */
        if (null === ($args = $this->getArgs()) or empty($args)) {
            Application::out('Invalid arguments, you need to provide command class and command.');
            return self::EXIT_FAILURE;
        }

        /**
         * @var string $commandClass
         * @var string $command
         * @var string $templatePath
         * List the provided arguments.
         */
        list($commandClass, $command) = $args;
        $templatePath = Kernel::getProjectRoot() . 'var/source/command_template.txt';

        /**
         * @var string $fileContent
         * @var string $filePath
         * Build command content from template.
         */
        $filePath = Kernel::getProjectRoot() . 'src/commands/' . $commandClass . '.php';
        if (file_exists($filePath)) {
            Application::out('Cannot create command "' . $commandClass .  '", file already exits!');
            return self::EXIT_FAILURE;
        }
        $fileContent = file_get_contents($templatePath);
        $fileContent = str_replace('%command_class%', $commandClass, $fileContent);
        $fileContent = str_replace('%command%', $command, $fileContent);
        file_put_contents($filePath, $fileContent);

        Application::out('Command "' . $commandClass .  '" successfully created!');
        return self::EXIT_SUCCESS;
    }

    /**
     * @package Vector
     * Vector\Command\CommandCreate->getCommandName()
     * @return void
     */
    public function getCommandName(): string
    {
        return 'vector:command-create';
    }

}
