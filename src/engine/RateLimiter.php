<?php
namespace Vector\Engine;
use Exception;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class RateExceededException extends Exception {}
class RateLimiter {

	private string $prefix;
	
	/**
     * @package Vector
     * __construct()
	 * @param {string} $token
	 * @param {string} $prefix = 'rate'
     */
	public function __construct(string $token, string $prefix = 'rate') {
		$this->prefix = md5($prefix . $token);
		if(!isset($_SESSION['cache'])) { $_SESSION['cache'] = array(); }
		if(!isset($_SESSION['expiries'])) {
			$_SESSION['expiries'] = array();
		} else { $this->expire_session_keys(); }
	}

	/**
	 * @package Vector
	 * Vector\Engine\RateLimiter->limit_requests_in_minutes()
	 * @param {int} $allowed_request
	 * @param {int} $minutes
	 * @return void
	 */
	public function limit_requests_in_minutes(int $allowed_requests, int $minutes): void {
		$this->expire_session_keys();
		$requests = 0;
		foreach ($this->get_keys($minutes) as $key) {
			$requests_in_current_minute = $this->get_session_key($key);
			if (false !== $requests_in_current_minute) $requests += $requests_in_current_minute;
		}
		if (false === $requests_in_current_minute) {
			$this->set_session_key($key, 1, ($minutes * 60 + 1));
		} else { $this->increment($key, 1); }
		if ($requests > $allowed_requests) throw new RateExceededException;
	}

	/**
	 * @package Vector
	 * Vector\Engine\RateLimiter->get_keys()
	 * @param {int} $minutes
	 * @return array
	 */
	private function get_keys(int $minutes): array {
		$keys = array();
		$now = time();
		for ($time = $now - $minutes * 60; $time <= $now; $time += 60) {
			$keys[] = $this->prefix . date('dHi', $time);
		}
		return $keys;
	}

	/**
	 * @package Vector
	 * Vector\Engine\RateLimiter->increment()
	 * @param {string} $key
	 * @param {int} $inc
	 * @return void
	 */
	private function increment(string $key, int $inc): void {
		$cnt = 0;
		if (isset($_SESSION['cache'][$key])) { $cnt = $_SESSION['cache'][$key]; }
		$_SESSION['cache'][$key] = $cnt + $inc;
	}

	/**
	 * @package Vector
	 * Vector\Engine\RateLimiter->set_session_key()
	 * @param {string} $key
	 * @param {int} $val
	 * @param {int} $expiry
	 * @return void
	 */
	private function set_session_key(string $key, string $val, string $expiry): void {
		$_SESSION['expiries'][$key] = time() + $expiry;
		$_SESSION['cache'][$key] = $val;
	}
	
	/**
	 * @package Vector
	 * Vector\Engine\RateLimiter->get_session_key()
	 * @param {string} $key
	 * @return mixed 
	 */
	private function get_session_key(string $key): mixed {
		return isset($_SESSION['cache'][$key]) ? $_SESSION['cache'][$key] : false;
	}

	/**
	 * @package Vector
	 * Vector\Engine\RateLimiter->expire_session_keys()
	 * @return void
	 */
	private function expire_session_keys(): void {
		foreach ($_SESSION['expiries'] as $key => $value) {
			if (time() > $value) { 
				unset($_SESSION['cache'][$key]);
				unset($_SESSION['expiries'][$key]);
			}
		}
	}

}