<?php

namespace Vector\Module\Transient;

use Vector\Module\Transient\AbstractTransient;
use Vector\Module\SqlConnection;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class SqlTransient extends AbstractTransient {

    protected SqlConnection $sql;
    protected mixed $data = null;
    protected null|int $time = null;

    /**
     * @package Vector
     * @param string $name
     * __construct()
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->sql = SqlConnection::getInstance();
        $transient = $this->sql->getResults("SELECT `data`, `time` 
            FROM `transients` 
            WHERE `name` = ? LIMIT 1", [
                ['type' => 's', 'value' => $this->name]
        ]);
        if ($transient['success'] AND !empty($transient['data'])) {
            $this->data = $transient['data']['data'];
            $this->time = $transient['data']['time'];
        }
    }

    /**
     * @package Vector
     * Vector\Module\Transient\SqlTransient->isValid() 
     * @param int $seconds
     * @return bool
     */
    public function isValid(int $seconds): bool
    {
        if (!$this->data) { return false; }
        return (time() - $this->time) > $seconds ? false : true;
    }

    /**
     * @package Vector
     * Vector\Module\Transient\SqlTransient->getData()
     * @return mixed
     */
    public function getData(): mixed 
    {
        if (!$this->data) { return null; }
        return unserialize($this->data);
    }

    /**
     * @package Vector
     * Vector\Module\Transient\SqlTransient->setData()
	 * @param mixed $data
     * @return bool
     */
    public function setData(mixed $data): bool 
    {
        if (!$this->data) {
            $execResult = $this->sql->exec("INSERT INTO `transients` 
                (`ID`, `name`, `data`, `time`) 
                VALUES (NULL, ?, ?, ?)", [
                    ['type' => 's', 'value' => $this->name],
                    ['type' => 's', 'value' => serialize($data)],
                    ['type' => 's', 'value' => time()]
            ]);
        } else {
            $execResult = $this->sql->exec("UPDATE `transients` 
                SET `data` = ?, `time` = ?
                WHERE `name` = ?", [
                    ['type' => 's', 'value' => serialize($data)],
                    ['type' => 's', 'value' => time()],
                    ['type' => 's', 'value' => $this->name]
            ]);
        }
        return $execResult['success'];
    }

    /**
     * @package Vector
     * Vector\Module\Transient\SqlTransient->delete()
     * @return bool
     */
    public function delete(): bool
    {
        $execResult = $this->sql->exec("DELETE FROM `transients` WHERE `name` = ?", [
            ['type' => 's', 'value' => $this->name]
        ]);
        return $execResult['success'];
    }

}