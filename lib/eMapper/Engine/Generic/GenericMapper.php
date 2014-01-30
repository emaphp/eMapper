<?php
namespace eMapper\Engine\Generic;

use eMapper\Statement\Configuration\StatementConfiguration;
use eMapper\Statement\Aggregate\StatementNamespaceAggregate;
use eMapper\Type\TypeHandler;
use eMapper\Cache\CacheProvider;
use eMapper\Cache\Key\CacheKey;
use eMapper\Reflection\Profiler;
use eMapper\Result\Mapper\ObjectTypeMapper;
use eMapper\Result\Mapper\ArrayTypeMapper;
use eMapper\Result\Mapper\ScalarTypeMapper;

abstract class GenericMapper {
	use StatementConfiguration;
	use StatementNamespaceAggregate;
	
	const OBJECT_TYPE_REGEX = '@^(?:object|obj+)(?::([A-z]{1}[\w|\\\\]*))?(?:<(\w+)(?::([A-z]{1}[\w]*))?>)?(\[\]|\[(\w+)(?::([A-z]{1}[\w]*))?\])?$@';
	const ARRAY_TYPE_REGEX  = '@^(?:array|arr+)(?:<(\w+)(?::([A-z]{1}[\w]*))?>)?(\[\]|\[(\w+)(?::([A-z]{1}[\w]*))?\])?$@';
	const SIMPLE_TYPE_REGEX = '@^([A-z]{1}[\w|\\\\]*)(\[\])?@';
	
	/**
	 * Type manager
	 * @var TypeManager
	 */
	public $typeManager;
	
	/**
	 * Applies default configuration options
	 */
	protected function applyDefaultConfig() {
		//database prefix
		$this->config['db.prefix'] = '';
		
		//dynamic sql environment id
		$this->config['environment.id'] = 'default';
		
		//dynamic sql environment class
		$this->config['environment.class'] = 'eMapper\Dynamic\Environment\DynamicSQLEnvironment';
		
		//use database prefix for procedure names
		$this->config['procedure.use_prefix'] = true;
		
		//default relation depth
		$this->config['depth.current'] = 0;
		
		//default relation depth limit
		$this->config['depth.limit'] = 1;
	}
	
	/**
	 * Assigns a TypeHandler instance for the given type
	 * @param string $type
	 * @param TypeHandler $typeHandler
	 * @throws \InvalidArgumentException
	 */
	public function addType($type, TypeHandler $typeHandler, $alias = null) {	
		$this->typeManager->setTypeHandler($type, $typeHandler);
	
		if (!is_null($alias)) {				
			$this->typeManager->addAlias($type, $alias);
		}
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
	 * Assigns a cache provider
	 * @param CacheProvider $provider
	 * @return MapperConfiguration
	 */
	public function setCacheProvider(CacheProvider $provider) {
		return $this->set('cache.provider', $provider);
	}
	
	/**
	 * Configures dynamic SQL environment
	 * @param string $id Environment id
	 * @param string $class Environment class
	 * @throws \InvalidArgumentException
	 */
	public function configureEnvironment($id, $class = 'eMapper\Dynamic\Environment\DynamicSQLEnvironment') {
		//apply values
		$this->config['environment.id'] = $id;
		$this->config['environment.class'] = $class;
	} 
	
	/**
	 * Obtains a clone of current instance without sensible configuration options
	 */
	public function safe_copy() {
		return $this->discard('map.type', 'map.params', 'map.result', 'map.parameter',
				'callback.query', 'callback.no_rows', 'callback.each', 'callback.filter',
				'cache.provider', 'cache.key', 'cache.ttl');
	}
	
	
	
	public function query($query) {
		/**
		 * INITIALIZE
		 */
		
		//validate query
		if (!is_string($query) || empty($query)) {
			$this->throw_exception("Query is not a valid string");
		}
		
		//check current mapper depth
		if ($this->config['depth.current'] > $this->config['depth.limit']) {
			return null;
		}

		//open connection
		$this->connect();
		
		/**
		 * OBTAIN PARAMETERS
		 */
		
		//get query and parameters
		$args = func_get_args();
		$query = array_shift($args);
		$parameterMap = null;
		
		//get parameter map
		if (array_key_exists('map.parameter', $this->config)) {
			$parameterMap = $this->config['map.parameter'];
		}
		
		/**
		 * CACHE CONTROL
		 */
		
		$cached_value = null;
		$cacheProvider = null;
		
		//check if there is a value stored in cache with the given key
		if (array_key_exists('cache.key', $this->config)) {
			//obtain cache provider
			$cacheProvider = $this->config['cache.provider'];
		
			//build cache key
			$cacheKeyBuilder = new CacheKey($this->typeManager, $parameterMap);
			$cacheKey = $cacheKeyBuilder->build($this->config['cache.key'], $args, $this->config);
		
			//check if key is present
			if ($cacheProvider->exists($cacheKey)) {
				$cached_value = $cacheProvider->fetch($cacheKey);
			}
		}
		
		if (is_null($cached_value)) {
			/**
			 * GENERATE QUERY
			 */
			
			//build statement
			$stmt = $this->build_statement($query, $args, $parameterMap);
			
			//override query
			if (array_key_exists('callback.query', $this->config)) {
				$query = call_user_func($callback, $stmt);
			
				if (!is_null($query)) {
					$stmt = $query;
				}
			}
			
			/**
			 * PARSE MAPPING EXPRESSION
			 */
			
			$resultMap = null;
			
			//build mapping callback
			if (array_key_exists('map.type', $this->config)) {
				//get mapping type
				$mapping_type = $this->config['map.type'];
					
				//object mapping type: object, object:class, object[column], object[column:type], etc
				if (preg_match(self::OBJECT_TYPE_REGEX, $mapping_type, $matches)) {
					//get class, if any
					if (empty($matches[1])) {
						$defaultClass = 'stdClass';
						$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
					}
					else {
						//remove leading ':'
						$defaultClass = $matches[1];
						$resultMap = null;
			
						//get result map
						if (array_key_exists('map.result', $this->config)) {
							$resultMap = $this->config['map.result'];
						}
						elseif (Profiler::isEntity($defaultClass)) {
							$resultMap = $defaultClass;
						}
					}
						
					//generate a new object mapper object
					$mapper = new ObjectTypeMapper($this->typeManager, $resultMap, $parameterMap, $defaultClass);
					$group = $group_type = $index = $index_type = null;
					
					if (count($matches) > 2) {
						//obtain group
						if (isset($matches[2]) && ($matches[2] == '0' || !empty($matches[2]))) {
							$group = $matches[2];
							$group_type = (isset($matches[3]) && !empty($matches[3])) ? $matches[3] : null;
							
							//check group type
							if (isset($group_type) && !in_array($group_type, $this->typeManager->getTypesList())) {
								$this->throw_exception("Unrecognized group type '$group_type'");
							}
						}
						
						//obtain index
						if (isset($matches[5]) && ($matches[5] == '0' || !empty($matches[5]))) {
							$index = $matches[5];
							$index_type = (isset($matches[6]) && !empty($matches[6])) ? $matches[6] : null;
								
							//check index type
							if (isset($index_type) && !in_array($index_type, $this->typeManager->getTypesList())) {
								$this->throw_exception("Unrecognized index type '$index_type'");
							}
						}
						
						//add index and group to mapper parameters
						$mapping_params = array($index, $index_type, $group, $group_type);
					}
					else {
						//add method
						$mapping_callback = array($mapper, 'mapResult');
					}
				}
				//array mapping type: array, array[], array[column], array[column:type]
				elseif (preg_match(self::ARRAY_TYPE_REGEX, $mapping_type, $matches)) {
					//obtain result map
					$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
			
					//generate a new array mapper object
					$mapper = new ArrayTypeMapper($this->typeManager, $resultMap, $parameterMap);
						
					if (count($matches) > 1) {
						$mapping_callback = array($mapper, 'mapList');
						$group = $group_type = $index = $index_type = null;
						
						//obtain group and group type
						if (isset($matches[1]) && ($matches[1] == '0' || !empty($matches[1]))) {
							$group = $matches[1];
							$group_type = (isset($matches[2]) && !empty($matches[2])) ? $matches[2] : null;
							
							//check group type
							if (isset($group_type) && !in_array($group_type, $this->typeManager->getTypesList())) {
								$this->throw_exception("Unrecognized group type '$group_type'");
							}
						}
						
						//obtain index and index type
						if (isset($matches[4]) && ($matches[4] == '0' || !empty($matches[4]))) {
							$index = $matches[4];
							$index_type = (isset($matches[5]) && !empty($matches[5])) ? $matches[5] : null;
							
							//check index type
							if (isset($index_type) && !in_array($index_type, $this->typeManager->getTypesList())) {
								$this->throw_exception("Unrecognized index type '$index_type'");
							}
						}
							
						//add index and group to mapper parameters
						$mapping_params = array($index, $index_type, $group, $group_type);
					}
					else {
						$mapping_callback = array($mapper, 'mapResult');
					}
				}
				//simple mapping type: integer, string, float, etc
				elseif (preg_match(self::SIMPLE_TYPE_REGEX, $mapping_type, $matches)) {
					//check type
					if (!in_array($matches[1], $this->typeManager->getTypesList())) {
						$this->throw_exception("Unrecognized type '{$matches[1]}'");
					}
						
					//get type handler
					$typeHandler = $this->typeManager->getTypeHandler($matches[1]);
						
					if ($typeHandler === false) {
						$this->throw_exception("Unknown type '{$matches[1]}'");
					}
						
					//set mapping callback
					$mapping_callback = array(new ScalarTypeMapper($typeHandler));
					$mapping_callback[] = empty($matches[2]) ? 'mapResult' : 'mapList';
				}
				else {
					$this->throw_exception("Unrecognized mapping expression '$mapping_type'");
				}
			}
			else {
				//obtain result map
				$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
					
				//generate mapper
				$mapper = new ArrayTypeMapper($this->typeManager, $resultMap, $parameterMap);
					
				//use default mapping type
				$mapping_callback = array($mapper, 'mapList');
				$mapping_params = array(null, null, null, null);
			}
			
			/**
			 * EXECUTE QUERY
			 */
			
			//run query
			$result = $this->run_query($stmt);
			
			//check query execution
			if ($result === false) {
				$this->throw_query_exception($stmt);
			}
			
			//check if result is successful
			if ($result === true) {
				//free result
				$this->free_result($result);
				return true;
			}
			
			/**
			 * INVOKE EMPTY RESULT CALLBACK
			 */
			
			$cacheable = true;
			
			//check if result is empty
			if ($result->num_rows === 0) {
				if (array_key_exists('callback.no_rows', $this->config)) {
					return call_user_func($this->config['callback.no_rows'], $result);
				}
			
				$cacheable = false;
			}
			
			/**
			 * ADD CUSTOM MAPPING OPTIONS
			 */
			
			//add defined mapping parameters
			if (array_key_exists('map.params', $this->config)) {
				if (!empty($mapping_params)) {
					$mapping_params = array_merge($mapping_params, $this->config['map.params']);
				}
				else {
					$mapping_params = $this->config['map.params'];
				}
			}
			
			//build mapping callback parameters
			if (isset($mapping_params)) {
				array_unshift($mapping_params, $this->build_result_interface($result));
			}
			else {
				$mapping_params = array($this->build_result_interface($result));
			}
			
			/**
			 * MAP RESULT
			 */
			
			//call mapping callback
			$mapped_result = call_user_func_array($mapping_callback, $mapping_params);
			
			//free result
			$this->free_result($result);
			
			/**
			 * EVALUATE RELATIONS
			 */
			
			if (isset($resultMap)) {
				$instance = $this->safe_copy();
				
				if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
					if (!empty($mapper->groupKeys)) {
						foreach ($mapper->groupKeys as $key) {
							for ($i = 0, $n = count($mapped_result[$key]); $i < $n; $i++) {
								$mapper->relate($mapped_result[$key][$i], $instance);
							}
						}
					}
					else {
						$keys = array_keys($mapped_result);
							
						foreach ($keys as $k) {
							$mapper->relate($mapped_result[$k], $instance);
						}
					}
				}
				elseif (!is_null($mapped_result)) {
					$mapper->relate($mapped_result, $instance);
				}
			}
			
			/**
			 * CACHE STORE
			 */
			
			//check if obtained value can be stored
			if (isset($cacheProvider) && $cacheable) {
				//store value
				if (array_key_exists('cache.ttl', $this->config)) {
					$cacheProvider->store($cacheKey, $mapped_result, (int) $this->config['cache.ttl']);
				}
				else {
					$cacheProvider->store($cacheKey, $mapped_result);
				}
			}
		}
		else {
			$mapped_result = $cached_value;
		}
		
		/**
		 * INVOKE TRAVERSING CALLBACK
		 */
		
		if (array_key_exists('callback.each', $this->config)) {
			$each_callback = $this->config['callback.each'];
				
			//generate a new safe instance
			$new_instance = $this->safe_copy();
		
			if ($each_callback instanceof \Closure) {
				//check if mapped result is a list
				if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
					if (!empty($mapper->groupKeys)) {
						foreach ($mapper->groupKeys as $key) {
							for ($i = 0, $n = count($mapped_result[$key]); $i < $n; $i++) {
								$each_callback->__invoke($mapped_result[$key][$i], $new_instance);
							}
						}
					}
					else {
						$keys = array_keys($mapped_result);
		
						for ($i = 0, $n = count($keys); $i < $n; $i++) {
							$each_callback->__invoke($mapped_result[$keys[$i]], $new_instance);
						}
					}
				}
				elseif (!is_null($mapped_result)) {
					$each_callback->__invoke($mapped_result, $new_instance);
				}
			}
			else {
				//this closure avoids getting "expected to be a reference"-style messages
				$c = function (&$mapped_result) use ($each_callback, $new_instance) {
					call_user_func($each_callback, $mapped_result, $new_instance);
				};
		
				//call traverse callback
				if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
					if (!empty($mapper->groupKeys)) {
						foreach ($mapper->groupKeys as $key) {
							for ($i = 0, $n = count($mapped_result[$key]); $i < $n; $i++) {
								$c->__invoke($mapped_result[$key][$i]);
							}
						}
					}
					else {
						$keys = array_keys($mapped_result);
		
						for ($i = 0, $n = count($keys); $i < $n; $i++) {
							$c->__invoke($mapped_result[$keys[$i]]);
						}
					}
				}
				elseif (!is_null($mapped_result)) {
					$c->__invoke($mapped_result);
				}
			}
		}
		
		/**
		 * INVOKE FILTER CALLBACK
		 */
		
		//apply filter
		if (array_key_exists('callback.filter', $this->config)) {
			$filter_callback = $this->config['callback.filter'];
				
			//check if mapped result is a list
			if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
				if (!empty($mapper->groupKeys)) {
					foreach ($mapper->groupKeys as $key) {
						$mapped_result[$key] = array_filter($mapped_result[$key], $filter_callback);
					}
				}
				else {
					$mapped_result = array_filter($mapped_result, $filter_callback);
				}
			}
			elseif (!is_null($mapped_result)) {
				if (!call_user_func($filter_callback, $mapped_result)) {
					$mapped_result = null;
				}
			}
		}
		
		return $mapped_result;
	}
	
	/*
	 * Abstract methods
	*/
	public abstract function run_query($query);
	public abstract function execute($statementId);
	public abstract function sql($query);
	public abstract function free_result($result);
	public abstract function commit();
	public abstract function rollback();
	public abstract function build_result_interface($result);
	
	/**
	 * Exception abstract methods
	 */
	
	public abstract function throw_exception($message);
	public abstract function throw_query_exception($query); 
}
?>