<?php 
namespace Vector\Engine;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    die(); 
}

class Throttler {

    private $frequency = 0;
    private $duration = 0;
    private $instances = [];

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(int $frequency, int $duration) {
        $this->frequency = $frequency;
        $this->duration = $duration;
    }

    /**
     * @package Vector
     * Vector\Engine\Throttler->await()
     */
    public function await(): void {
        $this->purge();
        $this->instances[] = microtime(true);
        if (!$this->is_free()) {
            $wait_duration = $this->duration_until_free();
            usleep($wait_duration);
        }
    }

    /**
     * @package Vector
     * Vector\Engine\Throttler->purge()
     */
    private function purge(): void {
        $cutoff = microtime(true) - $this->duration;
        $this->instances = array_filter($this->instances, function ($a) use ($cutoff) {
            return $a >= $cutoff;
        });
    }

    /**
     * @package Vector
     * Vector\Engine\Throttler->is_free()
     */
    private function is_free(): bool {
        return count($this->instances) < $this->frequency;
    }

    /**
     * @package Vector
     * Vector\Engine\Throttler->duration_until_free()
     */
    private function duration_until_free(): mixed {
        $oldest = $this->instances[0];
        $free_at = $oldest + $this->duration * 1000000;
        $now = microtime(true);
        return ($free_at < $now) ? 0 : $free_at - $now;
    }

}