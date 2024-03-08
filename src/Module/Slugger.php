<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Slugger
{

    /**
     * @package Vector
     * Vector\Module\Slugger::fromString()
     * @param string $input
     * @return string
     */
    public static function fromString(string $input): string
    {
        $input = preg_replace('/[^A-Za-z0-9\-]/', ' ', $input);
        $input = strtolower($input);
        $input = str_replace(' ', '-', $input);
        $input = preg_replace('/-+/', '-', $input);

        return trim($input, '-');
    }

}
