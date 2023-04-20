<?php
namespace Vector\Module;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.1 403 Forbidden');
    die(); 
}

class RateExceededException extends \Exception {}
class RateLimiter {

	protected string $prefix;
    protected Session $session;

	/**
     * @package Vector
     * __construct()
	 * @param string $token
	 * @param string $prefix
     */
	public function __construct(Request $request, string $prefix = 'rate') 
    {
		$this->prefix = md5($prefix . $request->getClientIp());
        $this->session = new Session();
        if (!$this->session->has('cache')) { $this->session->set('cache', []); }
        if ($this->session->has('expiries')) {
            $this->session->set('expiries', []);
        } else { $this->expireSessionKeys(); }
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->limitRequestsInMinutes()
	 * @param int $allowedRequests
	 * @param int $minutes
     * @return void
	 */
	public function limitRequestsInMinutes(int $allowedRequests, int $minutes): void 
    {
		$this->expireSessionKeys();
		$requests = 0;
		foreach ($this->getKeys($minutes) as $key) {
			$requestsInCurrentMinute = $this->getSessionKey($key);
			if (false !== $requestsInCurrentMinute) { $requests += $requestsInCurrentMinute; }
		}
		if (false === $requestsInCurrentMinute) {
			$this->setSessionKey($key, 1, ($minutes * 60 + 1));
		} else { $this->increment($key, 1); }
		if ($requests > $allowedRequests) { throw new RateExceededException; }
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->getKeys()
	 * @param int $minutes
     * @return array
	 */
	protected function getKeys(int $minutes): array 
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
	 * @param string $key
	 * @param int $inc
     * @return void
	 */
	protected function increment(string $key, int $inc): void 
    {
		$count = 0;
        if ($this->session->has('cache')) {
            $cache = $this->session->get('cache');
            if (isset($cache[$key])) { $count = $cache[$key]; }
        }
        $cache[$key] = $count + $inc;
        $this->session->set('cache', $cache);
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->setSessionKey()
	 * @param string $key
	 * @param int $val
	 * @param int $expiry
     * @return void
	 */
	protected function setSessionKey(string $key, string $val, string $expiry): void 
    {
        $expiries = $this->session->get('expiries');
        $cache = $this->session->get('cache');
        $expiries[$key] = time() + $expiry;
        $cache[$key] = $val;
        $this->session->set('expiries', $expiries);
        $this->session->set('cache', $cache);
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->getSessionKey()
	 * @param string $key
     * @return string|false
	 */
	protected function getSessionKey(string $key): string|false
    {
        $cache = $this->session->get('cache');
        return isset($cache[$key]) ? $cache[$key] : false;
	}

	/**
	 * @package Vector
	 * Vector\Module\RateLimiter->expireSessionKeys()
     * @return void
	 */
	protected function expireSessionKeys(): void 
    {	
		if (!$this->session->has('expiries')) { return; }
		foreach ($this->session->get('expiries') as $key => $value) {
			if (time() > $value) {
				$cache = $this->session->get('cache');
				$expiries = $this->session->get('expiries');
				unset($cache[$key]);
				unset($expiries[$key]);
				$this->session->set('cache', $cache);
				$this->session->set('expiries', $expiries);
			}
		}
	}

}