<?php

namespace Vector\Module\ApplicationLogger;

use Vector\Module\ApplicationLogger\AbstractLogger;
use Vector\Module\SqlConnection;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SqlLogger extends AbstractLogger
{

    protected SqlConnection $sql;

    /**
     * @package Vector
     * __construct()
     * @param string $type
     */
    public function __construct(string $type)
    {
        parent::__construct($type);
        $this->sql = SqlConnection::getInstance();
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\SqlLogger->write()
     * @param string $log
     * @return bool
     */
    public function write(string $log): bool
    {
        $execResult = $this->sql->exec("INSERT INTO `logs` 
            (`ID`, `domain`, `time`, `log`) 
            VALUES (NULL, ?, ?, ?)", [
                ['type' => 's', 'value' => $this->domain],
                ['type' => 's', 'value' => time()],
                ['type' => 's', 'value' => $log]
        ]);
        return $execResult['success'];
    }

}
