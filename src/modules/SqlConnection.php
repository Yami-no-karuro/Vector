<?php

namespace Vector\Module;

use mysqli;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SqlConnection {

    protected mysqli $mysqlitunnel;
    private static mixed $instance = null;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct()
    {
        global $config;
        $this->mysqlitunnel = new mysqli(
            $config->database->db_host,
            $config->database->db_user,
            $config->database->db_password,
            $config->database->db_name
        );
    }

    /**
     * @package Vector
     * __destruct()
     */
    public function __destruct()
    {
        $this->mysqlitunnel->close();
    }

    /**
     * @package Vector
     * Vector\Module\SqlConnection::getInstance()
     * @return SqlConnection
     */
    public static function getInstance(): SqlConnection
    {
        if (self::$instance == null) { self::$instance = new SqlConnection(); }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Module\SqlConnection->exec()
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function exec(string $sql, array $params = null): array
    {
        $cleanSql = $this->mysqlitunnel->prepare($sql);
        if ($params !== null) {
            $types = '';
            $values = [];
            foreach ($params as $param) {
                $types .= $param['type'];
                array_push($values, $param['value']);
            }
            $cleanSql->bind_param($types, ...$values);
        }
        $result = [
            'success' => false,
            'data' => [
                'inserted_id' => $cleanSql->insert_id,
                'affected_rows' => $cleanSql->affected_rows
            ]
        ];
        if (!$cleanSql->execute()) { return $result; }
        $result['success'] = true;
        return $result;
    }

    /**
     * @package Vector
     * Vector\Module\SqlConnection->getResults()
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getResults(string $sql, array $params = null): array
    {
        $cleanSql = $this->mysqlitunnel->prepare($sql);
        if ($params !== null) {
            $types = '';
            $values = [];
            foreach ($params as $param) {
                $types .= $param['type'];
                array_push($values, $param['value']);
            }
            $cleanSql->bind_param($types, ...$values);
        }
        if (!$cleanSql->execute()) { return ['success' => false, 'data' => NULL]; }
        $result = $cleanSql->get_result();
        $results = ['success' => true, 'data' => []];
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            if ($result->num_rows === 1) {
                $results['data'] = $row;
                break;
            }
            array_push($results['data'], $row);
        }
        return $results;
    }
}
