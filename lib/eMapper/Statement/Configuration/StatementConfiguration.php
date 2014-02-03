<?php
namespace eMapper\Statement\Configuration;

use eMapper\Configuration\Configuration;

trait StatementConfiguration {
	use Configuration;
	
	/**
	 * Sets result mapping options
	 * @param string $mapping_type
	 * @return StatementConfiguration
	 */
	public function type($mapping_type) {
		$args = func_get_args();
		$mapping_type = array_shift($args);
	
		//check if mapping arguments are defined
		if (empty($args)) {
			return $this->merge(['map.type' => $mapping_type]);
		}
	
		return $this->merge(['map.type' => $mapping_type, 'map.params' => $args]);
	}
	
	/**
	 * Sets the result map class to apply to obtained result
	 * @param mixed $result_map
	 * @return StatementConfiguration
	 */
	public function result_map($result_map) {
		if (!is_string($result_map)) {
			if (is_object($result_map)) {
				$result_map = get_class($result_map);
			}
			else {
				throw new \InvalidArgumentException("Method 'result_map' expects a string or valid a result map instance.");
			}
		}
		
		return $this->merge(['map.result' => $result_map]);
	}
	
	/**
	 * Sets the paramter map class to apply to given parameter
	 * @param mixed $parameter_map
	 * @return StatementConfiguration
	 */
	public function parameter_map($parameter_map) {
		if (!is_string($parameter_map)) {
			if (is_object($parameter_map)) {
				$parameter_map = get_class($parameter_map);
			}
			else {
				throw new \InvalidArgumentException("Method 'parameter_map' expects a string or valid a parameter map instance.");
			}
		}
		
		return $this->merge(['map.parameter' => $parameter_map]);
	}
	
	/**
	 * Sets the callback to invoke for each obtained row
	 * @param callable $callable
	 * @return StatementConfiguration
	 */
	public function each($callable) {
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException("Method 'each' expects a callable value.");
		}
		
		return $this->merge(['callback.each' => $callable]);
	}
	
	/**
	 * Sets the filter callback to apply on each obtained row
	 * @param callable $callable
	 * @return StatementConfiguration
	 */
	public function filter($callable) {
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException("Method 'filter' expects a callable value.");
		}
		
		return $this->merge(['callback.filter' => $callable]);
	}

	/**
	 * Sets a callback to handle empty results
	 * @param callable $callable
	 * @return StatementConfiguration
	 */
	public function no_rows($callable) {
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException("Method 'no_rows' expects a callable value.");
		}
		
		return $this->merge(['callback.no_rows' => $callable]);
	}
	
	/**
	 * Sets a callback which is called with the generated query
	 * Returning a value will override the query to execute
	 * @param callable $callable
	 * @return StatementConfiguration
	 */
	public function query_override($callable) {
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException("Method 'query_override' expects a callable value.");
		}
		
		return $this->merge(['callback.query' => $callable]);
	}
	
	/**
	 * Sets cache key and ttl
	 * @param string $cache_key
	 * @param integer $cache_ttl
	 * @return StatementConfiguration
	 */
	public function cache($cache_key, $cache_ttl = 0) {
		if (!is_string($cache_key) || empty($cache_key)) {
			throw new \InvalidArgumentException("Cache key is not a valid string.");
		}
		
		if (!is_integer($cache_ttl) || $cache_ttl < 0) {
			throw new \InvalidArgumentException("Cache TTL is not a valid integer.");
		}
	
		return $this->merge(['cache.key' => $cache_key, 'cache.ttl' => $cache_ttl]);
	}
	
	/**
	 * Sets relation depth limit
	 * @param integer $depth
	 * @throws \InvalidArgumentException
	 * @return StatementConfiguration
	 */
	public function depth($depth) {
		if (!is_integer($depth) || $depth <= 0) {
			throw new \InvalidArgumentException("Depth limit must be defined as a valid integer.");
		}
		
		return $this->merge(['depth.limit' => $depth]);
	}
	
	/**
	 * Sets index callback
	 * @param callable $callable
	 * @throws \InvalidArgumentException
	 */
	public function index($callable) {
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException("Method index expects a callable value");
		}
		
		return $this->merge(['callback.index' => $callable]);
	}
	
	/**
	 * Sets group callback
	 * @param callback $callable
	 * @throws \InvalidArgumentException
	 * @return Ambigous <\eMapper\Configuration\Configuration, \eMapper\Statement\Configuration\StatementConfiguration>
	 */
	public function group($callable) {
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException("Method group expects a callable value");
		}
		
		return $this->merge(['callback.group' => $callable]);
	}
}