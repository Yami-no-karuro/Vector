<?php

namespace Vector\Module\DataObject\Attribute;

use Attribute as DataObjectAttribute;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

#[DataObjectAttribute(DataObjectAttribute::TARGET_PROPERTY)]
class Property
{
    public function __construct(
        public ?string $name = null
    ) {}
}
