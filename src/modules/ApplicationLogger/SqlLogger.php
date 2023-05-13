<?php

namespace Vector\Module\ApplicationLogger;

use Vector\Module\ApplicationLogger\AbstractLogger;
use Vector\Module\SqlConnection;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class SqlLogger extends AbstractLogger {

    protected SqlConnection $sql;

    /**
     * @package Vector
     * @param string $type
     * __construct()
     */
    public function __construct(string $type)
    {
        parent::__construct($type);
        $this->sql = SqlConnection::getInstance();
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\FileSystemLogger->writeLog()
	 * @param string $content
     * @return bool
     */
    public function write(string $content): bool 
    {
        $execResult = $this->sql->exec("INSERT INTO `logs` 
            (`ID`, `type`, `content`) 
            VALUES (NULL, ?, ?)", [
                ['type' => 's', 'value' => $this->type],
                ['type' => 's', 'value' => $content]
        ]);
        return $execResult['success'];
    }

}