<?php

namespace Vector\Module;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use mysqli;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class SqlConnection {

    private mysqli $mysqlitunnel;
    private static mixed $instance = null;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct() {
        try {
            $this->mysqlitunnel = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        } catch (\Exception $e) { 
            $request = Request::createFromGlobals();
            $response = new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->prepare($request);
            $response->send();
            die();
        }
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
        $clean_sql = $this->mysqlitunnel->prepare($sql);
        if ($params !== null) {
            $types = '';
            $values = [];
            foreach($params as $param) { 
                $types .= $param['type'];
                array_push($values, $param['value']); 
            }
            $clean_sql->bind_param($types, ...$values);
        }
        $result = [
            'success' => false,
            'data'    => [
                'inserted_id'   => $clean_sql->insert_id,
                'affected_rows' => $clean_sql->affected_rows
            ]
        ];
        if (!$clean_sql->execute()) { return $result; }
        $result['success'] = true;
        return $result;
    }

    /**
     * @package Vector
     * Vector\Module\SqlConnection->get_results()
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function get_results(string $sql, array $params = null): array 
    {
        $clean_sql = $this->mysqlitunnel->prepare($sql);
        if ($params !== null) {
            $types = '';
            $values = [];
            foreach($params as $param) { 
                $types .= $param['type'];
                array_push($values, $param['value']); 
            }
            $clean_sql->bind_param($types, ...$values);
        }
        if (!$clean_sql->execute()) { 
            return [
                'success' => false,
                'data'    => NULL
            ]; 
        }
        $result = $clean_sql->get_result();
        $results = [
            'success' => true,
            'data'    => []
        ];
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