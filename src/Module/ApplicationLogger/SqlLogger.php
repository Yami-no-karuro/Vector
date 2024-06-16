<?php

namespace Vector\Module\ApplicationLogger;

use Vector\Module\ApplicationLogger\AbstractLogger;
use Vector\Module\SqlClient;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SqlLogger extends AbstractLogger
{
    protected PDO $sql;

    /**
     * @package Vector
     * __construct()
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        parent::__construct($domain);
        $this->sql = SqlClient::getInstance()
            ->getClient();
    }

    /**
     * @package Vector
     * Vector\Module\ApplicationLogger\SqlLogger->write()
     * @param string $log
     * @return void
     */
    public function write(string $log): void
    {
        $query = "INSERT INTO `vct_logs` (`ID`, `domain`, `time`, `log`) VALUES (NULL, :domain, :time, :log)";
        $q = $this->sql->prepare($query);

        $time = time();
        $q->bindParam('domain', $this->domain, PDO::PARAM_STR);
        $q->bindParam('time', $time, PDO::PARAM_INT);
        $q->bindParam('log', $log, PDO::PARAM_STR);
        $q->execute();
    }

}
