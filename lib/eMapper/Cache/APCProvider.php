<?php
namespace eMapper\Cache;

/**
 * The APCProvider class is cache provider providing data access to the apc extension
 * Requires the installation of the apc extension
 * @author emaphp
 */
class APCProvider implements CacheProvider {
	/**
	 * APCProvider constructor
	 * @throws \RuntimeException
	 */
	public function __construct() {
		if (!(extension_loaded('apc') && ini_get('apc.enabled'))) {
			throw new \RuntimeException("APC extension is not enabled on this server");
		}
	}
	
	public function store($id, $value, $ttl = 0) {
		return apc_store($id, $value, $ttl);
	}
	
	public function exists($id) {
		if (function_exists('apc_exists')) {
			return apc_exists($id);
		}
		
		return apc_fetch($id) === false ? false : true;
	}
	
	public function fetch($id) {
		return apc_fetch($id);
	}
	
	public function delete($id) {
		return apc_delete($id);
	}
}
?>