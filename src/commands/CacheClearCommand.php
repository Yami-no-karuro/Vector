<?php

namespace Vector\Command;

use Vector\Module\Console\AbstractCommand;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class CacheClearCommand extends AbstractCommand
{
    /**
     * @package Vector
     * Vector\Command\CacheClearCommand->execute()
     * @return int
     */
    public function execute(): int
    {
        $dir = __DIR__ . '/../../var/cache/';
        if (file_exists($dir) and is_dir($dir)) {
            $cacheDir = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($cacheDir, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $file) {
                $fname = $file->getFilename();
                if (!preg_match('%\.gitkeep$%', $fname)) {
                    $file->isDir() ? rmdir($file) : unlink($file);
                }
            }
        }
        return 0;
    }

    /**
     * @package Vector
     * Vector\Command\CacheClearCommand->setCommand()
     * @return void
     */
    public function setCommand(): void
    {
        $this->command = 'cache:clear';
    }

}
