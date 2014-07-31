<?php
namespace eMapper\Cache;

/**
 * The Memcache provider provides access to memcache instances through the Memcache class
 * Requires the Memcache library installed
 * @author emaphp
 */
class MemcacheProvider implements CacheProvider {
	/**
	 * Memcache object
	 * @var \Memcache
	 */
	public $memcache;
	
	/**
	 * Memcache flags
	 * @var integer
	 */
	public $flags;
	
	/**
	 * MemcacheProvider constructor
	 * @param string $host
	 * @param number $port
	 * @throws \RuntimeException
	 */
	public function __construct($host = 'localhost', $port = 11211) {
		//check if memcache is available
		if (!class_exists('\Memcache')) {
			throw new \RuntimeException("Memcache extension was not found on this server");
		}
		
		//connnect to Memcache server
		$this->memcache = @memcache_connect($host, $port);
		
		if ($this->memcache === false) {
			throw new \RuntimeException("Connection to memcache server on '" . $host. "' (" . $port . ") failed");
		}
	}
	
	public function store($id, $value, $ttl = 0) {
		return $this->memcache->set($id, $value, isset($this->flags) ? $this->flags : 0, $ttl);
	}
	
	public function exists($id) {
		$success = $this->memcache->add($id, 0);
		
		if (!$success) {
			return true;
		}
		
		$this->memcache->delete($id);
		return false;
	}
	
	public function fetch($id) {
		return $this->memcache->get($id, isset($this->flags) ? $this->flags : 0);
	}
	
	public function delete($id) {
		return $this->memcache->delete($id);
	}
	
	public function __call($method, $args) {
		return call_user_func_array(array($this->memcache, $method), $args);
	}
}
?>