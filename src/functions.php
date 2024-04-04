<?php

use Symfony\Component\HttpFoundation\Request;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

/**
 * @package Vector
 * getClassNamespace()
 * @param string $filepath
 * @param string $rootDirectory
 * @return ?string
 */
function getClassNamespace(string $filepath, string $root = 'src'): string
{
    $filepath = trim($filepath, '\\');
    if (!str_contains($filepath, $root)) {
        return null;
    }

    $path = explode('/', $filepath);
    $path[count($path) - 1] = pathinfo($path[count($path) - 1])['filename'];
    $namespace = array_slice($path, (array_search($root, $path) + 1));
    return implode('\\', ['\\Vector', ...$namespace]);
}


/**
 * @package Vector
 * getProjectRoot()
 * @return string
 */
function getProjectRoot(): string
{
    $workingDir = getcwd();
    if (str_contains($workingDir, 'public')) {
        return $workingDir . '/../';
    }

    return $workingDir . '/';
}


/**
 * @package Vector
 * getRequestUrl()
 * @param Request $request
 * @return string
 */
function getRequestUrl(Request &$request): string
{
    global $config;

    if (true === $config->dockerized) {
        return 'http://php-apache:80' . $request->getRequestUri();
    }

    $host = $request->getHost();
    $port = $request->getPort();
    $scheme = $request->getScheme();
    return $scheme . '://' . $host . ($port ? ':' . $port : '') . $request->getRequestUri();
}