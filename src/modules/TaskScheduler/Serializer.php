<?php

namespace Vector\Module\TaskScheduler;

use Closure;
use ReflectionFunction;
use SplFileObject;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Serializer 
{

    /**
     * @package Vector
     * Vector\Module\TaskScheduler\Serializer::serialize()
     * @return string
     */
    public static function serialize(Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);
        $file = new SplFileObject($reflection->getFileName());
        $file->seek($reflection->getStartLine() - 1);
        $code = '';
        while ($file->key() < $reflection->getEndLine()) {
            $code .= $file->current();
            $file->next();
        }
        $start = strpos($code, 'function');
        $end = strrpos($code, '}');
        return serialize(substr($code, $start, $end - $start + 1));
    }

}