<?php

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

/**
 * @package Vector
 * dump()
 * @param mixed $data
 * @return void
 */
function dump(mixed $data): void 
{
    echo '<pre>' . var_dump($data) . '</pre>';
}