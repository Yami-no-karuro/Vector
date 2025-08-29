<?php

namespace Vector\Module;

use Vector\Module\Transient\SqlTransient;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class SourceExplorer
{

    /**
     * @package Vector
     * Vector\Module\SourceExplorer::getWebpackBuildSources()
     * @return array 
     */
    public static function getWebpackBuildSources(): array
    {
        $transient = new SqlTransient('webpack-build-sources');
        if ($transient->isValid())
            return $transient->getData();
            
        $buildPath = getProjectRoot() . 'public/assets/build';
        $publicPath = '/assets/build';

        $sources = ['js' => [], 'css' => []];
        foreach (['js', 'css'] as $type) {
            $files = glob($buildPath . '/*.' . $type);
            foreach ($files as $file)
                $sources[$type][] = $publicPath . '/' . basename($file);
        }

        $transient->setData($sources, 900);
        return $sources;
    }
}
