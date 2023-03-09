<?php

namespace Vector\Module;

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
	public function __construct(string $token, string $prefix = 'rate') 
	{
		$this->prefix = md5($prefix . $token);
		if(!isset($_SESSION['cache'])) { $_SESSION['cache'] = array(); }
		if(!isset($_SESSION['expiries'])) {
			$_SESSION['expiries'] = array();
		} else { $this->expireSessionKeys(); }
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->limitRequestsInMinutes()
	 * @param {int} $allowed_request
	 * @param {int} $minutes
	 * @return void
	 */
	public function limitRequestsInMinutes(int $allowedRequests, int $minutes): void 
	{
		$this->expireSessionKeys();
		$requests = 0;
		foreach ($this->getKeys($minutes) as $key) {
			$requestsInCurrentMinute = $this->getSessionKey($key);
			if (false !== $requestsInCurrentMinute) $requests += $requestsInCurrentMinute;
		}
		if (false === $requestsInCurrentMinute) {
			$this->setSessionKey($key, 1, ($minutes * 60 + 1));
		} else { $this->increment($key, 1); }
		if ($requests > $allowedRequests) throw new RateExceededException;
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->getKeys()
	 * @param {int} $minutes
	 * @return array
	 */
	private function getKeys(int $minutes): array 
	{
		$keys = array();
		$now = time();
		for ($time = $now - $minutes * 60; $time <= $now; $time += 60) {
			$keys[] = $this->prefix . date('dHi', $time);
		}
		return $keys;
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->increment()
	 * @param {string} $key
	 * @param {int} $inc
	 * @return void
	 */
	private function increment(string $key, int $inc): void 
	{
		$cnt = 0;
		if (isset($_SESSION['cache'][$key])) { $cnt = $_SESSION['cache'][$key]; }
		$_SESSION['cache'][$key] = $cnt + $inc;
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->setSessionKey()
	 * @param {string} $key
	 * @param {int} $val
	 * @param {int} $expiry
	 * @return void
	 */
	private function setSessionKey(string $key, string $val, string $expiry): void 
	{
		$_SESSION['expiries'][$key] = time() + $expiry;
		$_SESSION['cache'][$key] = $val;
	}
	
	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->getSessionKey()
	 * @param {string} $key
	 * @return mixed 
	 */
	private function getSessionKey(string $key): mixed 
	{
		return isset($_SESSION['cache'][$key]) ? $_SESSION['cache'][$key] : false;
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->expireSessionKeys()
	 * @return void
	 */
	private function expireSessionKeys(): void 
	{
		foreach ($_SESSION['expiries'] as $key => $value) {
			if (time() > $value) { 
				unset($_SESSION['cache'][$key]);
				unset($_SESSION['expiries'][$key]);
			}
		}
	}

}