<?php

namespace Vector\Module\Dataobject;

use Vector\Module\DataObject\Attribute\Property;
use Vector\Module\EventDispatcher;
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
     * Vector\Module\DataObject\AbstractObject->save()
     * @return AbstractObject
     */
    public function save(): AbstractObject
    {
        $table = $this->tablename;
        $primaryKey = $this->primary;
        
        $fields = [];
        $values = [];
        $params = [];
    
        $reflection = new ReflectionObject($this);
        $props = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);
        
        EventDispatcher::dispatch('DataObjectListener', 'onPreSave', [&$this, &$props]);
        foreach ($props as $prop) {
            $attributes = $prop->getAttributes(Property::class);
            if (empty($attributes))
                continue;

            $decorator = $attributes[0];
            $decoratorArgs = $decorator->getArguments();
            
            $name = $prop->getName();
            if ($name === $primaryKey && $this->$name === null)
                continue;
                    
            $params[$name] = $this->$name;
            $fields[] = "`{$decoratorArgs['Column']}`";
            $values[] = ":{$name}";
        }
    
        $update = [];
        foreach ($fields as $field) {
            if ($field !== "`{$primaryKey}`")
                $update[] = "{$field} = VALUES({$field})";
        }
    
        $sql = "INSERT INTO `$table` (" . implode(',', $fields) . ")
            VALUES (" . implode(',', $values) . ")
            ON DUPLICATE KEY UPDATE " . implode(', ', $update);
    
        $stmt = $this->sql->prepare($sql);
        foreach ($params as $key => $val)
            $stmt->bindValue(":$key", $val);
        
        $stmt->execute();
        if (property_exists($this, $primaryKey) && $this->$primaryKey === null)
            $this->$primaryKey = $this->sql->lastInsertId();

        EventDispatcher::dispatch('DataObjectListener', 'onPostSave', [&$this]);
        return $this;
    }
    
    /**
     * @package Vector
     * Vector\Module\DataObject\AbstractObject->delete()
     * @return AbstractObject
     */
    public function delete(): AbstractObject
    {
        $table = $this->tablename;
        $primaryKey = $this->primary;
        if ($this->$primaryKey === null)
            return $this;

        EventDispatcher::dispatch('DataObjectListener', 'onPreDelete', [&$this]);
        $sql = "DELETE FROM `{$table}` WHERE `{$primaryKey}` = :id";
        
        $stmt = $this->sql->prepare($sql);
        $stmt->bindValue(':id', $this->$primaryKey);
        $stmt->execute();
        
        $this->$primaryKey = null;
        
        EventDispatcher::dispatch('DataObjectListener', 'onPostDelete', [&$this]);
        return $this;
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
}
