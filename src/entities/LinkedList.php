<?php
namespace Vector\Entities;
use Vector\Entities\ListNode;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    echo '403 Forbidden';
    die(); 
}

class LinkedList {

    private $first_node;
    private $last_node;
    private $count;

    /**
     * @package Vector
     * __construct()
     */
    function __construct() {
        $this->first_node = NULL;
        $this->last_node = NULL;
        $this->count = 0;
    }

    /**
     * @package Vector
     * Vector\Entities\LinkedList->insert_first();
     * @param {mixed} $data
     */
    public function insert_first(mixed $data): void {
        $link = new ListNode($data);
        $link->next = $this->first_node;
        $this->first_node = &$link;
        if ($this->last_node == NULL) { $this->last_node = &$link; }
        $this->count++;
    }

    /**
     * @package Vector
     * Vector\Entities\LinkedList->read_list();
     */
    public function read_list(): array {
        $list_data = array();
        $current = $this->first_node;
        while($current != NULL) {
            array_push($list_data, $current->read_node());
            $current = $current->next;
        }
        return $list_data;
    }

    /**
     * @package Vector
     * Vector\Entities\LinkedList->reverse_list();
     */
    public function reverse_list(): void {
        if($this->first_node != NULL) {
            if($this->first_node->next != NULL) {
                $current = $this->first_node;
                $new = NULL;
                while ($current !== NULL) {
                    $temp = $current->next;
                    $current->next = $new;
                    $new = $current;
                    $current = $temp;
                }
                $this->first_node = $new;
            }
        }
    }

}
