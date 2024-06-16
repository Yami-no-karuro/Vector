<?php

namespace Vector\Command;

use Vector\Module\Console\AbstractCommand;
use Vector\Module\ProcessRunner;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class CSFixer extends AbstractCommand
{
    /**
     * @package Vector
     * Vector\Command\CSFixer->execute()
     * @return int
     */
    public function execute(): int
    {
        $results = ProcessRunner::run('tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src');
        if (is_array($results)) {
            print_r($results['output']);
        }

        return self::EXIT_SUCCESS;
    }

    /**
     * @package Vector
     * Vector\Command\CSFixer->getCommandName()
     * @return void
     */
    public function getCommandName(): string
    {
        return 'vector:php-cs';
    }

}
