<?php 
namespace Vector\Functions;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    echo '403 Forbidden';
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
     * Vector\Functions\Throttler->await()
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
     * Vector\Functions\Throttler->purge()
     */
    private function purge(): array {
        $cutoff = microtime(true) - $this->duration;
        $this->instances = array_filter($this->instances, function ($a) use ($cutoff) {
            return $a >= $cutoff;
        });
    }

    /**
     * @package Vector
     * Vector\Functions\Throttler->is_free()
     */
    private function is_free(): bool {
        return count($this->instances) < $this->frequency;
    }

    /**
     * @package Vector
     * Vector\Functions\Throttler->duration_until_free()
     */
    private function duration_until_free(): string {
        $oldest = $this->instances[0];
        $free_at = $oldest + $this->duration * 1000000;
        $now = microtime(true);
        return ($free_at < $now) ? 0 : $free_at - $now;
    }

}