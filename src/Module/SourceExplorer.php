<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SourceExplorer
{

    /**
     * @package Vector
     * Vector\Module\SourceExplorer::fetchSources()
     * @return array 
     */
    public static function fetchSources(): array
    {
        $buildPath = getProjectRoot() . 'public/assets/build';
        $publicPath = '/assets/build';

        $sources = ['js' => [], 'css' => []];
        foreach (['js', 'css'] as $type) {
            $files = glob($buildPath . '/*.' . $type);
            foreach ($files as $file)
                $sources[$type][] = $publicPath . '/' . basename($file);
        }

        return $sources;
    }

}
