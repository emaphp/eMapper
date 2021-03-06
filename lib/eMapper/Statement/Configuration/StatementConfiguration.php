<?php
namespace eMapper\Statement\Configuration;

/**
 * The StatementConfiguration trait implements the method to configure a mapping object.
 * @author emaphp
 */
trait StatementConfiguration {
	use \FluentConfiguration;
	
	/**
	 * Sets result mapping options
	 * @param string $mapping_type
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function type($mapping_type) {
		$args = func_get_args();
		$mapping_type = array_shift($args);
	
		//check if mapping arguments are defined
		if (empty($args))
			return $this->merge(['map.type' => $mapping_type]);
	
		return $this->merge(['map.type' => $mapping_type, 'map.params' => $args]);
	}
	
	/**
	 * Sets the result map class to apply to obtained result
	 * @param mixed $result_map
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function resultMap($result_map) {
		if (!is_string($result_map)) {
			if (is_object($result_map))
				$result_map = get_class($result_map);
			else
				throw new \InvalidArgumentException("Method 'resultMap' expects a string or valid a result map instance.");
		}
		
		return $this->merge(['map.result' => $result_map]);
	}
	
	/**
	 * Sets the callback to invoke for each obtained row
	 * @param callable $callable
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function each($callable) {
		if (!is_callable($callable))
			throw new \InvalidArgumentException("Method 'each' expects a callable value.");
		
		return $this->merge(['callback.each' => $callable]);
	}
	
	/**
	 * Sets the filter callback to apply on each obtained row
	 * @param callable $callable
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function filterCallback($callable) {
		if (!is_callable($callable))
			throw new \InvalidArgumentException("Method 'filterCallback' expects a callable value.");
		
		return $this->merge(['callback.filter' => $callable]);
	}

	/**
	 * Sets a callback to handle empty results
	 * @param callable $callable
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function emptyCallback($callable) {
		if (!is_callable($callable))
			throw new \InvalidArgumentException("Method 'emptyCallback' expects a callable value.");
		
		return $this->merge(['callback.empty' => $callable]);
	}
	
	/**
	 * Sets a callback which is called with the generated query
	 * @param callable $callable
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function debug($callable) {
		if (!is_callable($callable))
			throw new \InvalidArgumentException("Method 'debug' expects a callable value.");
		
		return $this->merge(['callback.debug' => $callable]);
	}
	
	/**
	 * Sets cache key and ttl
	 * @param string $cache_key
	 * @param integer $cache_ttl
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function cache($cache_key, $cache_ttl = 0) {
		if (!is_string($cache_key) || empty($cache_key))
			throw new \InvalidArgumentException("Cache key is not a valid string.");
		
		if (!is_integer($cache_ttl) || $cache_ttl < 0)
			throw new \InvalidArgumentException("Cache TTL is not a valid integer.");
	
		return $this->merge(['cache.key' => $cache_key, 'cache.ttl' => $cache_ttl]);
	}
	
	/**
	 * Sets relation depth limit
	 * @param integer $depth
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function depth($depth) {
		if (!is_integer($depth) || $depth < 0)
			throw new \InvalidArgumentException("Depth limit must be defined as a valid integer.");
		
		return $this->merge(['depth.limit' => $depth]);
	}
	
	/**
	 * Sets index callback
	 * @param callable $callable
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function indexCallback($callable) {
		if (!is_callable($callable))
			throw new \InvalidArgumentException("Method 'indexCallback' expects a callable value");
		
		return $this->merge(['callback.index' => $callable]);
	}
	
	/**
	 * Sets group callback
	 * @param callback $callable
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Statement\Configuration\StatementConfiguration
	 */
	public function groupCallback($callable) {
		if (!is_callable($callable))
			throw new \InvalidArgumentException("Method 'groupCallback' expects a callable value");
		
		return $this->merge(['callback.group' => $callable]);
	}
}