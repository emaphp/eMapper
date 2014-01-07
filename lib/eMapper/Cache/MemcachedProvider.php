<?php
namespace eMapper\Cache;

class MemcachedProvider implements CacheProvider {
	/**
	 * Memcached instance
	 * @var \Memcached
	 */
	public $memcached;
	
	/**
	 * MemcachedProvider constructor
	 * @param string $cache_id
	 * @param string $options
	 * @throws \RuntimeException
	 */
	public function __construct($cache_id = null, $options = null) {
		//check installed library
		if (!class_exists('\Memcached')) {
			throw new \RuntimeException("Memcached extension was not found on this server");
		}
		
		//create a new memcached instance with the given id
		$this->memcached = is_null($cache_id) ? new \Memcached() : new \Memcached($cache_id);
		
		if (is_array($options) && !empty($options)) {
			// set instance options
			foreach ($options as $option => $value) {
				$this->memcached->setOption($option, $value);
			}
		}
	}
	
	public function store($id, $value, $ttl = 0) {
		return $this->memcached->set($id, $value, $ttl);
	}
	
	public function exists($id) {
		$success = $this->memcached->add($id, 0);
		
		if (!$success) {
			return true;
		}
		
		$this->memcached->delete($id);
		return false;
	}
	
	public function fetch($id) {
		return $this->memcached->get($id);
	}
	
	public function delete($id) {
		return $this->memcached->delete($id);
	}
	
	public function __call($method, $args) {
		return call_user_func_array(array($this->memcached, $method), $args);
	}
}