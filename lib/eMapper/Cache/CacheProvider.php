<?php
namespace eMapper\Cache;

interface CacheProvider {	
	/**
	 * Stores a value in cache
	 * @param string $id
	 * @param mixed $value
	 * @param int $ttl
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