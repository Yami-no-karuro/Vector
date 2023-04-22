<?php

namespace Vector\Module;

use Vector\Module\SqlConnection;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class ApplicationLogger {

    protected string $type;
    protected ?string $filepath;
    protected ?SqlConnection $sql;

    /**
     * @package Vector
     * @param string $type
     * __construct()
     */
    public function __construct(string $type) 
    {
        $this->type = $type;
        if (true === DATABASE_LOGS) {
            $this->sql = SqlConnection::getInstance();
        } else { $this->filepath = __DIR__ . '/../var/logs/' . $this->type . '.log.txt'; }
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger->writeLog()
	 * @param string $content
     * @return bool
     */
    public function writeLog(string $content): bool 
    {
        if (true === DATABASE_LOGS) {
            $execResult = $this->sql->exec("INSERT INTO `logs` 
                (`ID`, `type`, `content`) 
                VALUES (NULL, ?, ?)", [
                    ['type' => 's', 'value' => $this->type],
                    ['type' => 's', 'value' => $content]
            ]);
            return $execResult['success'];
        } else { return @file_put_contents($this->filepath, $content, FILE_APPEND | LOCK_EX); }
    }

}