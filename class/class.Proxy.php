<?php

use function PHPSTORM_META\type;

require_once __DIR__."/class.Proxy.php";

class Proxy {
	private $ip, $auth, $latency, $working;

	/**
	 * @param string|array $proxy xxx.xxx.xxx.xxx:port | Array containing keys: ip, latency, working, auth
	 * @param string $auth [optional] user:password
	 * @param int $latency [optional]
	 * @param bool $auth [optional]
	 */
	public function __construct($proxy, $auth = null, $latency = Config::PROXY_TEST_MAX_TIME_MS, $working = false)
	{
		if (is_string($proxy)) {
			$this->ip = $proxy;
			$this->auth = $auth;
			$this->latency = $latency;
			$this->working = $working;
			return;
		}

		if (is_array($proxy)) { // If arg $ip is an array, it most likely comes from a DB query.
			$this->ip = $proxy["ip"];
			$this->auth = $proxy["auth"];
			$this->latency = $proxy["latency"];
			$this->working = $proxy["working"];
		}
	}

	public function __toString() {
		return $this->ip;
	}


	public function getIP() { return $this->ip; }
	public function getAuth() { return $this->auth; }
	public function getLatency() { return $this->latency; }
	public function isWorking() { return $this->working; }
	public function isAuthed() { return !empty($this->auth); }
}