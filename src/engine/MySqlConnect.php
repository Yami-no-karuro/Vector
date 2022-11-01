<?php
namespace Vector\Engine;
use Vector\Objects\Response;
use Exception;
use mysqli;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class MySqlConnect {

    private $mysqlitunnel;
    private static $instance;

    /**
     * @package Vector
     * __construct()
     */
    private function __construct() {
        try {
            $this->mysqlitunnel = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        } catch (Exception $e) { 
            $response = new Response(NULL, ['HTTP/1.1 500 Internal Server Error']);
            $response->send(true);
        }
    }

    /**
     * @package Vector
     * __destruct()
     */
    public function __destruct() {
        $this->mysqlitunnel->close();
    }

    /**
     * @package Vector
     * Vector\Engine\MySqlConnect::get_instance()
     */
    public static function get_instance(): object {
        if (self::$instance == null) { self::$instance = new MySqlConnect();  }
        return self::$instance;
    }

    /**
     * @package Vector
     * Vector\Engine\MySqlConnect->exec()
     * @param {string} $sql
     * @param {array} $params
     * @return {bool}
     */
    public function exec(string $sql, array $params = null): array {
        $clean_sql = $this->mysqlitunnel->prepare($sql);
        if ($params !== null) {
            $types = '';
            $values = array();
            foreach($params as $param) { 
                $types .= $param['type'];
                array_push($values, $param['value']); 
            }
            $clean_sql->bind_param($types, ...$values);
        }
        $result = array(
            'success' => false,
            'data'    => array(
                'inserted_id'   => $clean_sql->insert_id,
                'affected_rows' => $clean_sql->affected_rows
            )
        );
        if (!$clean_sql->execute()) { return $result; }
        $result['success'] = true;
        return $result;
    }

    /**
     * @package Vector
     * Vector\Engine\MySqlConnect->get_results()
     * @param {string} $sql
     * @param {array} $params
     * @return {array | bool}
     */
    public function get_results(string $sql, array $params = null): array {
        $clean_sql = $this->mysqlitunnel->prepare($sql);
        if ($params !== null) {
            $types = '';
            $values = array();
            foreach($params as $param) { 
                $types .= $param['type'];
                array_push($values, $param['value']); 
            }
            $clean_sql->bind_param($types, ...$values);
        }
        if (!$clean_sql->execute()) { 
            return array(
                'success' => false,
                'data'    => NULL
            ); 
        }
        $result = $clean_sql->get_result();
        $results = array(
            'success' => true,
            'data'    => array()
        );
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) { array_push($results['data'], $row); }
        return $results;
    }

}