<?php

namespace Vector\Object;

use Vector\Module\AbstractObject;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class ExampleObject extends AbstractObject {

    protected ?string $exampleAttribute;

    /** @param string $attr */
    public function __construct(string $attr = null)
    {
        parent::__construct();
        $this->exampleAttribute = $attr;
    }

    /** 
     * @param string $attr
     * @return void 
     */
    public function setExampleAttribute(string $attr): void
    {
        $this->exampleAttribute = $attr;
    }

    /** @return string|null */
    public function getExampleAttribute(): string|null
    {
        return $this->exampleAttribute;
    }

}