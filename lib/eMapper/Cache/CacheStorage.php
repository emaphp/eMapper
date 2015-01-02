<?php
namespace eMapper\Cache;

/**
 * The CacheStorage trait implements the logic for adding the required cache metadata in stored values. 
 * @author emaphp
 */
trait CacheStorage {
	/**
	 * Injects cache metadata on the given value
	 * @param array | object $value
	 * @param string $class
	 * @param string $method
	 * @param array $groups
	 * @param string $resultMap
	 */
	protected function injectCacheMetadata(&$value, $class, $method, $groups, $resultMap) {
		$metadata = new \stdClass();
		$metadata->class = $class;
		$metadata->method = $method;
		$metadata->groups = empty($groups) ? null : $groups;
		$metadata->resultMap = $resultMap;
	
		$metakey = $this->config['cache.metakey'];
		if (is_array($value) || $value instanceof \ArrayObject)
			$value[$metakey] = $metadata;
		else
			$value->$metakey = $metadata;
	}
	
	/**
	 * Extracts cache metadata from the given value
	 * @param array | object $value
	 * @return NULL | \stdClass
	 */
	protected function extractCacheMetadata($value) {
		$metakey = $this->config['cache.metakey'];
		if (is_array($value) || $value instanceof \ArrayObject) {
			if (!array_key_exists($metakey, $value))
				return null;
				
			return $value[$metakey];
		}
		elseif (is_object($value)) {
			if (!property_exists($value, $metakey))
				return null;
				
			return $value->$metakey;
		}
	
		return null;
	}
}