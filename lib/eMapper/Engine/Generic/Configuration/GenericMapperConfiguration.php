<?php
namespace eMapper\Engine\Generic\Configuration;

use eMapper\Cache\CacheProvider;
use eMapper\Statement\Configuration\StatementConfiguration;

trait GenericMapperConfiguration {
	use StatementConfiguration;
	
	/**
	 * Assigns a cache provider
	 * @param CacheProvider $provider
	 * @return MapperConfiguration
	 */
	public function setCacheProvider(CacheProvider $provider) {
		return $this->set('cache.provider', $provider);
	}
	
	/**
	 * Sets database prefix
	 * @param string $prefix
	 * @return MapperConfiguration
	 */
	public function setPrefix($prefix) {
		if (!is_string($prefix)) {
			throw new \InvalidArgumentException("Database prefix must be speciied as a string");
		}
		
		return $this->set('db.prefix', $prefix);
	}
	
	/**
	 * Sets dynamic SQL execution environment class 
	 * @param string $classname
	 * @throws \InvalidArgumentException
	 */
	public function setEnvironment($classname) {
		if (!is_string($classname) || empty($classname)) {
			throw new \InvalidArgumentException("Environment class must be defined as a valid classname");
		}
		
		return $this->set('db.environment', $classname);
	}
	
	/**
	 * Obtains a clone of current instance without sensible configuration options
	 */
	public function safe_copy() {
		return $this->discard('map.type', 'map.params', 'map.result', 'map.parameter',
				'callback.each', 'callback.no_rows', 'callback.query', 'callback.result', 'callback.filter',
				'cache.provider', 'cache.key', 'cache.ttl');
	}
}
?>