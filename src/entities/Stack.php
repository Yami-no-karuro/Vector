<?php
namespace Vector\Entities;
use Exception;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    die(); 
}

class StackException extends Exception {}
class Stack {

    protected $stack;
    protected $limit;
    
    /**
     * @package Vector
     * __construct()
     */
    public function __construct($limit = 10) {
        $this->stack = array();
        $this->limit = $limit;
    }

    /**
     * @package Vector
     * Vector\Entities\Stack->push()
     * @param {mixed} $item
     */
    public function push(mixed $item): void {
        if (count($this->stack) < $this->limit) {
            array_unshift($this->stack, $item);
        } else { throw new StackException; }
    }

    /**
     * @package Vector
     * Vector\Entities\Stack->pop()
     */
    public function pop(): array {
        if ($this->isEmpty()) {
	        throw new StackException;
	    } else { return array_shift($this->stack); }
    }

    /**
     * @package Vector
     * Vector\Entities\Stack->top();
     */
    public function top(): mixed {
        return current($this->stack);
    }

    /**
     * @package Vector
     * Vector\Entities\Stack->is_empty()
     */
    public function is_empty(): bool {
        return empty($this->stack);
    }
    
}