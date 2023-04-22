<?php

/*
MySql
----
CREATE TABLE `<dbname>`.`transients` (
    `ID` INT NOT NULL AUTO_INCREMENT , 
    `trs_key` VARCHAR(50) NOT NULL , 
    `trs_value` TEXT NOT NULL , 
    `trs_ltmtime` INT NOT NULL , 
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB; 
----
*/

namespace Vector\Module;

use Vector\Module\SqlConnection;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class Transient {

    protected string $transient;
    public mixed $content = null;
    public null|int $lsmTime = null;

    protected null|string $filepath;
    protected null|SqlConnection $sql;

    /**
     * @package Vector
     * @param string $transient
     * __construct()
     */
    public function __construct(string $transient) 
    {
        $this->transient = $transient;
        if (true === DATABASE_TRANSIENTS) {
            $this->sql = SqlConnection::getInstance();
            $transient = $this->sql->getResults("SELECT `trs_value`, `trs_ltmtime` 
                FROM `transients` 
                WHERE `trs_key` = ?", [
                    ['type' => 's', 'value' => $this->transient]
            ]);
            if ($transient['success'] AND !empty($transient['data'])) {
                $this->content = $transient['data']['trs_value'];
                $this->lsmTime = $transient['data']['trs_ltmtime'];
            }
        } else {
            $this->filepath = __DIR__ . '/../var/cache/transients/' . md5($transient);
            $this->content = @file_get_contents($this->filepath, true);
            $this->lsmTime = @filemtime($this->filepath);
        }
    }

    /**
     * @package Vector
     * Vector\Module\Transient->isValid() 
     * @param int $seconds
     * @return bool
     */
    public function isValid(int $seconds): bool
    {
        if (!$this->lsmTime) { return false; }
        return (time() - $this->lsmTime) > $seconds ? false : true;
    }

    /**
     * @package Vector
     * Vector\Module\Transient->getContent()
     * @return mixed
     */
    public function getContent(): mixed 
    {
        if (!$this->content) { return null; }
        return unserialize($this->content);
    }

    /**
     * @package Vector
     * Vector\Module\Transient->setContent()
	 * @param mixed $data
     * @return bool
     */
    public function setContent(mixed $data): bool 
    {
        $srlData = serialize($data);
        if (true === DATABASE_TRANSIENTS) {
            if (!$this->content) {
                $execResult = $this->sql->exec("INSERT INTO `transients` 
                    (`ID`, `trs_key`, `trs_value`, `trs_ltmtime`) 
                    VALUES (NULL, ?, ?, ?)", [
                        ['type' => 's', 'value' => $this->transient],
                        ['type' => 's', 'value' => serialize($data)],
                        ['type' => 's', 'value' => time()]
                ]);
            } else {
                $execResult = $this->sql->exec("UPDATE `transients` 
                    SET `trs_value` = ?, `trs_ltmtime` = ?
                    WHERE `trs_key` = ?", [
                        ['type' => 's', 'value' => serialize($data)],
                        ['type' => 's', 'value' => time()],
                        ['type' => 's', 'value' => $this->transient]
                ]);
            }
            return $execResult['success'];
        } else { return @file_put_contents($this->filepath, $srlData); }
    }

    /**
     * @package Vector
     * Vector\Module\Transient->delete()
     * @return bool
     */
    public function delete(): bool
    {
        if (true === DATABASE_TRANSIENTS) {
            $execResult = $this->sql->exec("DELETE FROM `transients` WHERE `trs_key` = ?", [
                ['type' => 's', 'value' => $this->transient]
            ]);
            return $execResult['success'];
        } else { return @unlink($this->filepath); }
    }

}