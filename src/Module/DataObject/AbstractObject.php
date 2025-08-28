<?php

namespace Vector\Module\Dataobject;

use Vector\Module\SqlClient;
use ReflectionObject;
use ReflectionProperty;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

abstract class AbstractObject
{
    protected string $tablename;
    protected string $primary;
    protected PDO $sql;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(array $data = [])
    {
        $this->sql = SqlClient::getInstance()
            ->getClient();

        foreach (array_keys($data) as $key) {
            $prop = $this->formatAsProperty($key);
            $this->$prop = $data[$key];
        }
    }

    /**
     * @package Vector
     * Vector\Module\DataObject\AbstractObject->get()
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return isset($this->$key) ? $this->$key : null;
    }

    /**
     * @package Vector
     * Vector\Module\DataObject\AbstractObject->set()
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, mixed $value): void
    {
        if (isset($this->$key))
            $this->$key = $value;
    }

    /**
     * @package Vector
     * Vector\Module\DataObject\AbstractObject->formatAsProperty()
     * @param string $key
     * @return string
     */
    protected function formatAsProperty(string $key): string
    {
        $parts = preg_split('/[_]+/', $key);
        $formatted = array_shift($parts);
        foreach ($parts as $part)
            $formatted .= ucfirst($part);

        return $formatted;
    }
    
    /**
     * @package Vector
     * Vector\Module\DataObject\AbstractObject->save()
     * @return void
     */
    public function save(): void
    {
        $reflection = new ReflectionObject($this);
        $props = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);
        
        $fields = [];
        $values = [];
        $params = [];
    
        foreach ($props as $prop) {
            $name = $prop->getName();
            if ($name === $this->primary && $this->$name === null) 
                continue;
                
            $fields[] = "`$name`";
            $values[] = ":$name";
            $params[$name] = $this->$name;
        }
    
        $update = [];
        foreach ($fields as $field) {
            if ($field !== "`$this->primary`")
                $update[] = "$field = VALUES($field)";
        }
    
        $sql = "INSERT INTO `$this->tablename` (" . implode(',', $fields) . ")
            VALUES (" . implode(',', $values) . ")
            ON DUPLICATE KEY UPDATE " . implode(', ', $update);
    
        $stmt = $this->sql->prepare($sql);
        foreach ($params as $key => $val)
            $stmt->bindValue(":$key", $val);
        
        $stmt->execute();
        if (property_exists($this, $this->primary) && $this->$this->primary === null)
            $this->$this->primary = $this->sql->lastInsertId();
    }
    
    /**
     * @package Vector
     * Vector\Module\DataObject\AbstractObject->delete()
     * @return void
     */
    public function delete(): void
    {
        if ($this->$this->primary === null) 
            return;
    
        $sql = "DELETE FROM `$this->tablename` WHERE `$this->primary` = :id";
        $stmt = $this->sql->prepare($sql);
        $stmt->bindValue(':id', $this->$this->primary);
        $stmt->execute();
    }
}
