<?php
namespace Vector\Entities;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    echo '403 Forbidden';
    die(); 
}

class ListNode {

    public $data;
    public $next;
    
    /**
     * @package Vector
     * __construct()
     */
    function __construct($data) {
        $this->data = $data;
        $this->next = NULL;
    }

    /**
     * @package Vector
     * Vector\Entities\ListNode->read_node()
     */
    function read_node(): mixed {
        return $this->data;
    }
    
}