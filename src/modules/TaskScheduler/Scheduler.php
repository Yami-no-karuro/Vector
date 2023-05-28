<?php

namespace Vector\Module\TaskScheduler;

use Vector\Module\SqlClient;
use Vector\Module\TaskScheduler\Serializer;
use Closure;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Scheduler
{

    protected SqlClient $sql;
    private static mixed $instance = null;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct()
    {
        $this->sql = SqlClient::getInstance();
    }
    
    /**
     * @package Vector
     * Vector\Module\TaskScheduler\Scheduler::getInstance()
     * @return Scheduler
     */
    public static function getInstance(): Scheduler
    {
        if (self::$instance == null) {
            self::$instance = new Scheduler();
        }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Module\TaskScheduler\Scheduler->schedule()
     * @param Closure $closure 
     * @param array $params
     * @return bool
     */
    public function schedule(Closure $closure, array $params): bool
    {
        $result = $this->sql->exec("INSERT INTO `scheduled_tasks`
            (`ID`, `callback`, `params`, `time`)
            VALUES (NULL, ?, ?, ?)", [
                ['type' => 's', 'value' => Serializer::serialize($closure)],
                ['type' => 's', 'value' => serialize($params)],
                ['type' => 's', 'value' => time()]
        ]);
        return $result['success'];
    }

}