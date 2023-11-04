<?php

namespace Vector\Module;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class StopWatch
{
    protected float $startTime;
    protected float $endTime;

    /**
     * @package Vector
     * Vector\Module\StopWatch->start()
     * @return void
     */
    public function start(): void
    {
        $this->startTime = microtime(true);
    }

    /**
     * @package Vector
     * Vector\Module\StopWatch->stop()
     * @return void
     */
    public function stop(): void
    {
        $this->endTime = microtime(true);
    }

    /**
     * @package Vector
     * Vector\Module\StopWatch->getElapsed()
     * @return string
     */
    public function getPartial(): string
    {
        $timePassed = microtime(true) - $this->startTime;
        $minutes = floor($timePassed / 60);
        $seconds = $timePassed % 60;
        $milliseconds = ($timePassed - floor($timePassed)) * 1000;
        return sprintf("%02d:%02d:%02d", $minutes, $seconds, $milliseconds);
    }

    /**
     * @package Vector
     * Vector\Module\StopWatch->getElapsed()
     * @return string
     */
    public function getElapsed(): string
    {
        $timePassed = $this->endTime - $this->startTime;
        $minutes = floor($timePassed / 60);
        $seconds = $timePassed % 60;
        $milliseconds = ($timePassed - floor($timePassed)) * 1000;
        return sprintf("%02d:%02d:%02d", $minutes, $seconds, $milliseconds);
    }

}
