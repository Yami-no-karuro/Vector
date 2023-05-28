<?php

namespace Vector\Command;

use Vector\Module\Console\AbstractCommand;
use Vector\Module\SqlClient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SchedulerConsume extends AbstractCommand
{
    protected SqlClient $sql;

    /**
     * @package Vector
     * __construct()
     * @param array $args
     */
    public function __construct(?array $args)
    {
        parent::__construct($args);
        $this->sql = SqlClient::getInstance();
    }

    /**
     * @package Vector
     * Vector\Command\SchedulerConsume->execute()
     * @return int
     */
    public function execute(): int
    {
        $tasks = $this->sql->getResults("SELECT * FROM `scheduled_tasks` ORDER BY `ID`");
        if ($tasks['success'] and !empty($tasks['data'])) {
            foreach ($tasks['data'] as $task) {
                $params = unserialize($task['params']);
                $callback = unserialize($task['callback']);
                $callable = function() {};
                eval('$callable = ' . $callback . ';');
                $callable(...$params);
                $this->sql->exec("DELETE FROM `scheduled_tasks` WHERE `ID` = ?", [
                    ['type' => 'd', 'value' => $task['ID']]
                ]);
            }
        }
        return 0;
    }

    /**
     * @package Vector
     * Vector\Command\SchedulerConsume->getCommandName()
     * @return void
     */
    public function getCommandName(): string
    {
        return 'vector:scheduler-consume';
    }

}
