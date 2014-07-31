<?php
namespace eMapper\Cache;

/**
 * The CacheProvider interface defines the methods that a cache provider class must implement
 * @author emaphp
 */
interface CacheProvider {
	/**
	 * Stores a value in cache
	 * @param string $id Cache key
	 * @param mixed $value The value to store
	 * @param int $ttl Time to live
	 */
	public function store($id, $value, $ttl = 0);
	
	/**
	 * Checks if a value exists in cache
	 * @param string $id
	 */
	public function exists($id);
	
	/**
	 * Obtains a value from cache
	 * @param string $id
	 */
	public function fetch($id);
	
	/**
	 * Deletes a value from cache
	 * @param string $id
	 */
	public function delete($id);
}
?>