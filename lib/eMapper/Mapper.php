<?php
namespace eMapper;

use eMapper\SQL\Configuration\StatementConfiguration;
use eMapper\SQL\Aggregate\SQLNamespaceAggregate;
use eMapper\Type\TypeHandler;
use eMapper\Cache\CacheProvider;
use eMapper\Engine\Generic\Driver;
use eMapper\Cache\Key\CacheKey;
use eMapper\Reflection\Profiler;
use eMapper\Result\Mapper\ObjectTypeMapper;
use eMapper\Result\Mapper\ArrayTypeMapper;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\TypeManager;
use eMapper\SQL\EntityNamespace;

class Mapper {
	use StatementConfiguration;
	use SQLNamespaceAggregate;
	
	//mapping expression regex
	const OBJECT_TYPE_REGEX = '@^(?:object|obj+)(?::([A-z]{1}[\w|\\\\]*))?(?:<(\w+)(?::([A-z]{1}[\w]*))?>)?(\[\]|\[(\w+)(?::([A-z]{1}[\w]*))?\])?$@';
	const ARRAY_TYPE_REGEX  = '@^(?:array|arr+)(?:<(\w+)(?::([A-z]{1}[\w]*))?>)?(\[\]|\[(\w+)(?::([A-z]{1}[\w]*))?\])?$@';
	const SIMPLE_TYPE_REGEX = '@^([A-z]{1}[\w|\\\\]*)(\[\])?@';
	
	/**
	 * Database driver
	 * @var Driver
	 */
	public $driver;
	
	/**
	 * Type manager
	 * @var TypeManager
	 */
	public $typeManager;
	
	/**
	 * Cache provider
	 * @var CacheProvider
	 */
	public $cacheProvider;
	
	public function __construct(Driver $driver) {
		$this->driver = $driver;
		
		//obtain defaul type handler
		$this->typeManager = $this->driver->build_type_manager();
		
		//build configuration
		$this->applyDefaultConfig();
		$this->config = array_merge($this->config, $driver->config);
	}
	
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
		$this->config['proc.use_prefix'] = true;
		
		//wrap procedure name between quotes (PostgreSQL)
		$this->config['proc.wrap'] = true;
		
		//procedure returning values directly (PostgreSQL)
		$this->config['proc.as_table'] = false;
	
		//default relation depth
		$this->config['depth.current'] = 0;
	
		//default relation depth limit
		$this->config['depth.limit'] = 1;
	}
	
	public function __safe_copy() {
		return $this->discard('map.type', 'map.params', 'map.result', 'map.parameter',
				'callback.query', 'callback.no_rows', 'callback.each', 'callback.filter', 'callback.index', 'callback.group',
				'cache.key', 'cache.ttl',
				'proc.types');
	}
	
	/**
	 * Stores database prefix
	 * @param string $prefix
	 * @throws \InvalidArgumentException
	 */
	public function setPrefix($prefix) {
		if (!is_string($prefix)) {
			throw new \InvalidArgumentException("Database prefix must be speciied as a string");
		}
	
		return $this->set('db.prefix', $prefix);
	}
	
	/**
	 * Adds a new type handler
	 * @param string $type
	 * @param TypeHandler $typeHandler
	 * @param string $alias
	 */
	public function addType($type, TypeHandler $typeHandler, $alias = null) {
		$this->typeManager->setTypeHandler($type, $typeHandler);
	
		if (!is_null($alias)) {
			$this->typeManager->addAlias($type, $alias);
		}
	}
	
	/**
	 * Assigns a cache provider to current instance
	 * @param CacheProvider $provider
	 */
	public function setCacheProvider(CacheProvider $provider) {
		$this->cacheProvider = $provider;
	}
	
	/**
	 * Configures dynamic sql environment
	 * @param string $id
	 * @param string $class
	 */
	public function setEnvironment($id, $class = 'eMapper\Dynamic\Environment\DynamicSQLEnvironment') {
		//apply values
		$this->config['environment.id'] = $id;
		$this->config['environment.class'] = $class;
	}
	
	/**
	 * Returns current database connection
	 * @return mixed
	 */
	public function getConnection() {
		return $this->driver->connection;
	}
	
	/**
	 * Initializes a database connection
	 */
	public function connect() {
		return $this->driver->connect();
	}
	
	/**
	 * Executes a query
	 * @param string $query
	 * @return mixed
	 */
	public function query($query) {
		/**
		 * INITIALIZE
		 */
		
		//validate query
		if (!is_string($query) || empty($query)) {
			$this->driver->throw_exception("Query is not a valid string");
		}
		
		//validate current depth
		if ($this->config['depth.current'] > $this->config['depth.limit']
		&& (!array_key_exists('map.type', $this->config)
		|| (preg_match(self::OBJECT_TYPE_REGEX, $this->config['map.type']) || preg_match(self::ARRAY_TYPE_REGEX, $this->config['map.type'])))) {
			return null;
		}
		
		//open connection
		$this->driver->connect();
		
		/**
		 * OBTAIN PARAMETERS
		 */
		
		//get query and parameters
		$args = func_get_args();
		$query = array_shift($args);
		$parameterMap = array_key_exists('map.parameter', $this->config) ? $this->config['map.parameter'] : null;
		
		/**
		 * CACHE CONTROL
		 */
		
		$cached_value = $cacheProvider = null;
		
		//check if there is a value stored in cache with the given key
		if (array_key_exists('cache.key', $this->config) && isset($this->cacheProvider)) {
			//obtain cache provider
			$cacheProvider = $this->cacheProvider;
		
			//build cache key
			$cacheKeyBuilder = new CacheKey($this->typeManager, $parameterMap);
				
			try {
				$cache_key = $cacheKeyBuilder->build($this->config['cache.key'], $args, $this->config);
			}
			catch (\Exception $e) {
				$this->throw_exception($e->getMessage());
			}
		
			//check if key is present
			if ($cacheProvider->exists($cache_key)) {
				$cached_value = $cacheProvider->fetch($cache_key);
			}
		}
		
		//current instance copy
		$safe_clone = null;
		
		if (is_null($cached_value)) {
			/**
			 * GENERATE QUERY
			 */
				
			//build statement
			try {
				$stmt = $this->driver->build_statement($this->typeManager, $parameterMap);
				$stmt = $stmt->build($query, $args, $this->config);
			}
			catch (\Exception $e) {
				$this->driver->throw_exception($e->getMessage(), $e);
			}
			
			//override query
			if (array_key_exists('callback.query', $this->config)) {
				$query = call_user_func($this->config['callback.query'], $stmt);
					
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
						elseif (Profiler::getClassProfile($defaultClass)->isEntity()) {
							$resultMap = $defaultClass;
						}
					}
					
					//generate a new object mapper object
					$mapper = new ObjectTypeMapper($this->typeManager, $resultMap, $defaultClass);
					$group = $group_type = $index = $index_type = null;
					
					if (count($matches) > 2) {
						$mapping_callback = array($mapper, 'mapList');
					
						//obtain group
						if (array_key_exists('callback.group', $this->config)) {
							$group = $this->config['callback.group'];
							$group_type = 'callable';
						}
						elseif (isset($matches[2]) && ($matches[2] == '0' || !empty($matches[2]))) {
							$group = $matches[2];
							$group_type = (isset($matches[3]) && !empty($matches[3])) ? $matches[3] : null;
								
							//check group type
							if (isset($group_type) && !in_array($group_type, $this->typeManager->getTypesList())) {
								$this->driver->throw_exception("Unrecognized group type '$group_type'");
							}
						}
					
						//obtain index
						if (array_key_exists('callback.index', $this->config)) {
							$index = $this->config['callback.index'];
							$index_type = 'callable';
						}
						elseif (isset($matches[5]) && ($matches[5] == '0' || !empty($matches[5]))) {
							$index = $matches[5];
							$index_type = (isset($matches[6]) && !empty($matches[6])) ? $matches[6] : null;
					
							//check index type
							if (isset($index_type) && !in_array($index_type, $this->typeManager->getTypesList())) {
								$this->driver->throw_exception("Unrecognized index type '$index_type'");
							}
						}
					
						//add index and group to mapper parameters
						$mapping_params = [$index, $index_type, $group, $group_type];
					}
					else {
						//add method
						$mapping_callback = [$mapper, 'mapResult'];
					}
				}
				//array mapping type: array, array[], array[column], array[column:type]
				elseif (preg_match(self::ARRAY_TYPE_REGEX, $mapping_type, $matches)) {
					//obtain result map
					$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
						
					//generate a new array mapper object
					$mapper = new ArrayTypeMapper($this->typeManager, $resultMap);
					
					if (count($matches) > 1) {
						$mapping_callback = [$mapper, 'mapList'];
						$group = $group_type = $index = $index_type = null;
					
						//obtain group and group type
						if (array_key_exists('callback.group', $this->config)) {
							$group = $this->config['callback.group'];
							$group_type = 'callable';
						}
						elseif (isset($matches[1]) && ($matches[1] == '0' || !empty($matches[1]))) {
							$group = $matches[1];
							$group_type = (isset($matches[2]) && !empty($matches[2])) ? $matches[2] : null;
								
							//check group type
							if (isset($group_type) && !in_array($group_type, $this->typeManager->getTypesList())) {
								$this->driver->throw_exception("Unrecognized group type '$group_type'");
							}
						}
					
						//obtain index and index type
						if (array_key_exists('callback.index', $this->config)) {
							$index = $this->config['callback.index'];
							$index_type = 'callable';
						}
						elseif (isset($matches[4]) && ($matches[4] == '0' || !empty($matches[4]))) {
							$index = $matches[4];
							$index_type = (isset($matches[5]) && !empty($matches[5])) ? $matches[5] : null;
								
							//check index type
							if (isset($index_type) && !in_array($index_type, $this->typeManager->getTypesList())) {
								$this->driver->throw_exception("Unrecognized index type '$index_type'");
							}
						}
							
						//add index and group to mapper parameters
						$mapping_params = [$index, $index_type, $group, $group_type];
					}
					else {
						$mapping_callback = [$mapper, 'mapResult'];
					}
				}
				//simple mapping type: integer, string, float, etc
				elseif (preg_match(self::SIMPLE_TYPE_REGEX, $mapping_type, $matches)) {
					//check type
					if (!in_array($matches[1], $this->typeManager->getTypesList())) {
						$this->driver->throw_exception("Unrecognized type '{$matches[1]}'");
					}
					
					//get type handler
					$typeHandler = $this->typeManager->getTypeHandler($matches[1]);
					
					if ($typeHandler === false) {
						$this->driver->throw_exception("Unknown type '{$matches[1]}'");
					}
						
					//create mapper instance
					$mapper = new ScalarTypeMapper($typeHandler);
						
					//set mapping callback
					$mapping_callback = [$mapper];
					$mapping_callback[] = empty($matches[2]) ? 'mapResult' : 'mapList';
				}
				else {
					$this->driver->throw_exception("Unrecognized mapping expression '$mapping_type'");
				}
			}
			else {
				//obtain result map
				$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
					
				//generate mapper
				$mapper = new ArrayTypeMapper($this->typeManager, $resultMap);
					
				//use default mapping type
				$mapping_callback = [$mapper, 'mapList'];
				
				//check for group callback
				if (array_key_exists('callback.group', $this->config)) {
					$group = $this->config['callback.group'];
					$group_type = 'callable';
				}
				else {
					$group = $group_type = null;
				}
				
				//check for index callback
				if (array_key_exists('callback.index', $this->config)) {
					$index = $this->config['callback.index'];
					$index_type = 'callable';
				}
				else {
					$index = $index_type = null;
				}
				
				$mapping_params = [$index, $index_type, $group, $group_type];
			}
			
			/**
			 * EXECUTE QUERY
			 */
				
			//run query
			$result = $this->driver->query($stmt);
				
			//check query execution
			if ($result === false) {
				$this->driver->throw_query_exception($stmt);
			}
			
			//check if result is successful
			if ($result === true) {
				//free result
				$this->driver->free_result($result);
				return true;
			}
			
			/**
			 * INVOKE EMPTY RESULT CALLBACK
			 */
				
			$cacheable = true;
			$ri = $this->driver->build_result_iterator($result);
				
			//check if result is empty
			if ($ri->countRows() === 0) {
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
				array_unshift($mapping_params, $ri);
			}
			else {
				$mapping_params = [$ri];
			}
			
			/**
			 * MAP RESULT
			 */
				
			//call mapping callback
			try {
				$mapped_result = call_user_func_array($mapping_callback, $mapping_params);
			}
			catch (\Exception $e) {
				$this->driver->throw_exception($e->getMessage(), $e);
			}
				
			//free result
			$this->driver->free_result($result);
			
			/**
			 * EVALUATE RELATIONS
			 */
				
			if (isset($resultMap)) {
				$safe_clone = $this->__safe_copy();
			
				if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
					if (!empty($mapper->groupKeys)) {
						foreach ($mapper->groupKeys as $key) {
							$indexes = array_keys($mapped_result[$key]);
								
							for ($i = 0, $n = count($indexes); $i < $n; $i++) {
								$mapper->relate($mapped_result[$key][$indexes[$i]], $parameterMap, $safe_clone);
							}
						}
					}
					else {
						$keys = array_keys($mapped_result);
							
						foreach ($keys as $k) {
							$mapper->relate($mapped_result[$k], $parameterMap, $safe_clone);
						}
					}
				}
				elseif (!is_null($mapped_result)) {
					$mapper->relate($mapped_result, $parameterMap, $safe_clone);
				}
			}
			
			/**
			 * CACHE STORE
			 */
				
			//check if obtained value can be stored
			if (isset($cacheProvider) && $cacheable) {
				//store value
				if (array_key_exists('cache.ttl', $this->config)) {
					$cacheProvider->store($cache_key, $mapped_result, intval($this->config['cache.ttl']));
				}
				else {
					$cacheProvider->store($cache_key, $mapped_result);
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
			//generate a new safe instance
			if (is_null($safe_clone)) {
				$safe_clone = $this->__safe_copy();
			}
				
			$each_callback = $this->config['callback.each'];
		
			if ($each_callback instanceof \Closure) {
				//check if mapped result is a list
				if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
					if (!empty($mapper->groupKeys)) {
						foreach ($mapper->groupKeys as $key) {
							$indexes = array_keys($mapped_result[$key]);
								
							for ($i = 0, $n = count($indexes); $i < $n; $i++) {
								$each_callback->__invoke($mapped_result[$key][$indexes[$i]], $safe_clone);
							}
						}
					}
					else {
						$keys = array_keys($mapped_result);
		
						for ($i = 0, $n = count($keys); $i < $n; $i++) {
							$each_callback->__invoke($mapped_result[$keys[$i]], $safe_clone);
						}
					}
				}
				elseif (!is_null($mapped_result)) {
					$each_callback->__invoke($mapped_result, $safe_clone);
				}
			}
			else {
				//this closure avoids getting "expected to be a reference"-style messages
				$c = function (&$mapped_result) use ($each_callback, $safe_clone) {
					call_user_func($each_callback, $mapped_result, $safe_clone);
				};
		
				//call traverse callback
				if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
					if (!empty($mapper->groupKeys)) {
						foreach ($mapper->groupKeys as $key) {
							$indexes = array_keys($mapped_result[$key]);
								
							for ($i = 0, $n = count($indexes); $i < $n; $i++) {
								$c->__invoke($mapped_result[$key][$indexes[$i]]);
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
	
	/**
	 * Executes a declared statement
	 * @param string $statementId
	 * @return mixed
	 */
	public function execute($statementId) {
		//obtain parameters
		$args = func_get_args();
		$statementId = array_shift($args);
		
		if (!is_string($statementId) || empty($statementId)) {
			$this->driver->throw_exception("Statement id is not a valid string");
		}
		
		//obtain statement
		$stmt = $this->getStatement($statementId);
		
		if ($stmt === false) {
			$this->driver->throw_exception("Statement '$statementId' could not be found");
		}
		
		//get statement config
		$query = $stmt->query;
		$options = $stmt->options;
		
		//add query to method parameters
		array_unshift($args, $query);
		
		//run query
		return (empty($options)) ? call_user_func_array([$this, 'query'], $args) : call_user_func_array([$this->merge($options->config, true), 'query'], $args);
	}
	
	/**
	 * Runs a query
	 * @param string $query
	 * @return mixed
	 */
	public function sql($query) {
		if (!is_string($query) || empty($query)) {
			$this->driver->throw_exception("Query is not a valid string");
		}
	
		//open connection
		$this->driver->connect();
	
		//get query and parameters
		$args = func_get_args();
		$query = array_shift($args);
	
		//build statement
		$stmt = $this->driver->build_statement($this->typeManager, array_key_exists('map.parameter', $this->config) ? $this->config['map.parameter'] : null);
	
		//run query
		$result = $this->driver->query($stmt->build($query, $args, $this->config));
	
		//check query execution
		if ($result === false) {
			$this->driver->throw_query_exception($stmt);
		}
	
		return $result;
	}
	
	public function __call($method, $args) {
		//include database prefix
		if (array_key_exists('proc.use_prefix', $this->config) && $this->config['proc.use_prefix'] === true) {
			//get database prefix
			$db_prefix = array_key_exists('db.prefix', $this->config) ? $this->config['db.prefix'] : '';
	
			//build procedure name
			$procedure_name = $db_prefix . $method;
		}
		else {
			$procedure_name = $method;
		}
	
		$tokens = array();
	
		//check if argument types are defined
		if (array_key_exists('proc.types', $this->config)) {
			$parameter_types = $this->config['proc.types'];
	
			//build argument types string
			foreach ($parameter_types as $type) {
				$tokens[] = '%{' . $type . '}';
			}
		}
	
		//fill tokens array
		for ($i = count($tokens), $n = count($args); $i < $n; $i++) {
			$tokens[] = '%{' . $i . '}';
		}
	
		//check if tokens exceeds parameters
		if (count($tokens) > count($args)) {
			$tokens = array_slice($tokens, 0, count($args));
		}
		
		$call = $this->driver->build_call($procedure_name, $tokens, $this->config);
		
		array_unshift($args, $call);
		
		//call query
		return call_user_func_array([$this, 'query'], $args);
	}
	
	/**
	 * Returns a manager instance for the given class name
	 * @param string $classname
	 * @return \eMapper\Manager
	 */
	public function buildManager($classname) {
		return new Manager($this, Profiler::getClassProfile($classname));
	}
	
	/**
	 * Adds a new entity namespace to current instance
	 * @param EntityNamespace $namespace
	 */
	public function addEntityNamespace(EntityNamespace $namespace) {
		$namespace->setDriver($this->driver);
		$this->addNamespace($namespace);
	}
	
	/**
	 * Closes a database connection
	 */
	public function close() {
		return $this->driver->close();
	}
	
	/**
	 * Returns las generated error
	 */
	public function lastError() {
		return $this->driver->get_last_error();
	}
	
	/**
	 * Returns last generated id
	 */
	public function lastId() {
		return $this->driver->get_last_id();
	}
	
	/*
	 * TRANSACTION METHODS
	 */
	
	/**
	 * Begins a transaction
	 */
	public function beginTransaction() {
		return $this->driver->begin();
	}
	
	/**
	 * Commits current transaction
	 */
	public function commit() {
		return $this->driver->commit();
	}
	
	/**
	 * Rollbacks current transaction
	 */
	public function rollback() {
		return $this->driver->rollback();
	}
}

?>