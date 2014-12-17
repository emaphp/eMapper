<?php
namespace eMapper;

use eMapper\Statement\Configuration\StatementConfiguration;
use eMapper\Engine\Generic\Statement\StatementFormatter;
use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profiler;
use eMapper\Result\Mapper\ComplexMapper;
use eMapper\Result\Mapper\ObjectMapper;
use eMapper\Result\Mapper\EntityMapper;
use eMapper\Result\Mapper\StdClassMapper;
use eMapper\Result\Mapper\ArrayMapper;
use eMapper\Result\Mapper\ArrayObjectMapper;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\TypeManager;
use eMapper\Type\TypeHandler;
use eMapper\Cache\Key\CacheKeyFormatter;
use eMapper\Procedure\StoredProcedure;
use eMapper\Query\FluentQuery;
use eMapper\Reflection\ClassProfile;
use SimpleCache\CacheProvider;

/**
 * The Mapper class manages how a database result is converted to a given type.
 * @author emaphp
 */
class Mapper {
	use StatementConfiguration;
	
	//mapping expression regex
	const OBJECT_TYPE_REGEX = '@^(?:object|obj+)(?::([A-z]{1}[\w|\\\\]*))?(?:<(\w+)(?::([A-z]{1}[\w]*))?>)?(\[\]|\[(\w+)(?::([A-z]{1}[\w]*))?\])?$@';
	const ARRAY_TYPE_REGEX  = '@^(?:array|arr+)(?:<(\w+)(?::([A-z]{1}[\w]*))?>)?(\[\]|\[(\w+)(?::([A-z]{1}[\w]*))?\])?$@';
	const SIMPLE_TYPE_REGEX = '@^([A-z]{1}[\w|\\\\]*)(\[\])?@';
	
	//transaction status
	const TX_NOT_STARTED = 0;
	const TX_STARTED = 1;
	
	/**
	 * Database driver
	 * @var \eMapper\Engine\Generic\Driver
	 */
	protected $driver;
	
	/**
	 * Type manager
	 * @var \eMapper\Type\TypeManager
	 */
	protected $typeManager;
		
	/**
	 * Cache provider
	 * @var \SimpleCache\CacheProvider
	 */
	protected $cacheProvider;
	
	/**
	 * Transaction status
	 * @var int
	 */
	protected $txStatus = self::TX_NOT_STARTED;
	
	/**
	 * Transaction internal counter
	 * @var int
	 */
	protected $txCounter = 0;
	
	public function __construct(Driver $driver) {
		$this->driver = $driver;
		
		//obtain default type handler
		$this->typeManager = $this->driver->buildTypeManager();
		
		//build configuration
		$this->setDefaultConfig();
	}
	
	/**
	 * Applies default configuration options
	 */
	protected function setDefaultConfig() {
		//database prefix
		$this->config['db.prefix'] = '';
	
		//dynamic sql environment id
		$this->config['environment.id'] = 'default';
	
		//dynamic sql environment class
		$this->config['environment.class'] = 'eMapper\Dynamic\Environment\DynamicSQLEnvironment';
	
		//default relation depth
		$this->config['depth.current'] = 0;
	
		//default relation depth limit
		$this->config['depth.limit'] = 1;
		
		//cache metakey
		$this->config['cache.metakey'] = '__cache__';
	}
	
	/*
	 * CLONING METHODS
	 */
	
	/**
	 * Generates a copy of the current instance without transient options
	 * @return \eMapper\Mapper
	 */
	public function __copy() {
		return $this->discard(
			'map.type', 'map.params', 'map.result',
			'callback.empty', 'callback.each', 'callback.filter', 'callback.index', 'callback.group',
			'cache.key', 'cache.ttl'
		);
	}
	
	/**
	 * Generates a copy of the current instance with an incremented depth
	 * @return \eMapper\Mapper
	 */
	public function __icopy() {
		$obj = $this->__copy();
		$obj->setOption('depth.current', $this->config['depth.current'] + 1);
		return $obj;
	}
	
	/**
	 * Stores database prefix
	 * @param string $prefix
	 * @throws \InvalidArgumentException
	 */
	public function setPrefix($prefix) {
		if (!is_string($prefix))
			throw new \InvalidArgumentException("Database prefix must be specified as a string");
		$this->setOption('db.prefix', $prefix);
	}
	
	/**
	 * Adds a new type handler
	 * @param string $type
	 * @param \eMapper\Type\TypeHandler $typeHandler
	 * @param string | array $alias
	 */
	public function addType($type, TypeHandler $typeHandler, $alias = null) {
		$this->typeManager->setTypeHandler($type, $typeHandler);
	
		if (!is_null($alias)) {
			if (is_array($alias)) {
				foreach ($alias as $al)
					$this->typeManager->addAlias($type, $al);
			}
			else
				$this->typeManager->addAlias($type, $alias);
		}
	}
	
	/**
	 * Configures dynamic sql environment
	 * @param string $id
	 * @param string $class
	 */
	public function configureEnvironment($id, $class = 'eMapper\Dynamic\Environment\DynamicSQLEnvironment') {
		$this->setOption('environment.id', $id);
		$this->setOption('environment.class', $class);
	}
	
	/**
	 * Returns current database connection
	 * @return object | resorce
	 */
	public function getConnection() {
		return $this->driver->getConnection();
	}
	
	/**
	 * Initializes a database connection
	 */
	public function connect() {
		return $this->driver->connect();
	}
	
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
		$metadata->groups = $groups;
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
	
	/**
	 * Executes a query
	 * @param string $query
	 * @return mixed
	 */
	public function query($query) {
		/*
		 * INITIALIZE
		 */
		
		//validate query
		if (!is_string($query) || empty($query))
			throw new \InvalidArgumentException("Query is not a valid string");

		//open connection
		$this->driver->connect();
		
		/*
		 * OBTAIN PARAMETERS
		 */
		
		//get query and parameters
		$args = func_get_args();
		$query = array_shift($args);
		
		/*
		 * CACHE CONTROL
		 */
		
		$cached_value = $resultMap = null;
		$cacheable = false; //if mapped value is cacheable
		
		//check if there is a value stored in cache with the given key
		if (array_key_exists('cache.key', $this->config) && isset($this->cacheProvider)) {
			//build cache key
			$cacheKeyFormatter = new CacheKeyFormatter($this->typeManager);
			$cache_key = $cacheKeyFormatter->format($this->config['cache.key'], $args, $this->config);

			//check if key is present
			if ($this->cacheProvider->exists($cache_key)) {
				$cached_value = $this->cacheProvider->fetch($cache_key);
				
				if (is_array($cached_value) || is_object($cached_value)) {
					//get cache metadata
					$cache_metadata = $this->extractCacheMetadata($cached_value);
					
					//no metadata, assume is a scalar value
					if (is_null($cache_metadata)) 
						return $cached_value;
					
					//create mapper instance
					$rc = new \ReflectionClass($cache_metadata->class);
					$mapper = $rc->newInstance($this->driver->buildTypeManager(), $cache_metadata->resultMap);
					$mapper->setGroupKeys($cache_metadata->groups);
					
					//build mapping callback
					$mapping_callback = [$mapper, $cache_metadata->method];
					
					//set resultmap
					$resultMap = $cache_metadata->resultMap;
				}
			}
		}
		
		if (is_null($cached_value)) {
			/*
			 * GENERATE QUERY
			 */
				
			//build statement
			$statementFormatter = $this->driver->buildStatement($this->typeManager);
			$stmt = $statementFormatter->format($query, $args, $this->config);
			
			//debug query
			if (array_key_exists('callback.debug', $this->config)) 
				call_user_func($this->config['callback.debug'], $stmt);
			
			/*
			 * PARSE MAPPING EXPRESSION
			 */

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
						$mapper = new StdClassMapper($this->typeManager, $resultMap);
					}
					else {
						//remove leading ':'
						$defaultClass = strtolower($matches[1]);
						$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
						
						if ($defaultClass == 'stdclass')
							$mapper = new StdClassMapper($this->typeManager, $resultMap);
						elseif ($defaultClass == 'arrayobject')
							$mapper = new ArrayObjectMapper($this->typeManager, $resultMap);
						elseif (Profiler::getClassProfile($matches[1])->isEntity()) {
							$mapper = new EntityMapper($this->typeManager, $matches[1]);
							$resultMap = $matches[1];
						}
						else {
							$mapper = new ObjectMapper($this->typeManager, $matches[1]);
							$resultMap = null;
						}
					}
					
					//generate a new object mapper object
					$group = $group_type = $index = $index_type = null;
					
					if (count($matches) > 2) {
						$mapping_callback = [$mapper, 'mapList'];
					
						//obtain group
						if (array_key_exists('callback.group', $this->config)) {
							$group = $this->config['callback.group'];
							$group_type = 'callable';
						}
						elseif (isset($matches[2]) && ($matches[2] == '0' || !empty($matches[2]))) {
							$group = $matches[2];
							$group_type = (isset($matches[3]) && !empty($matches[3])) ? $matches[3] : null;
						}
					
						//obtain index
						if (array_key_exists('callback.index', $this->config)) {
							$index = $this->config['callback.index'];
							$index_type = 'callable';
						}
						elseif (isset($matches[5]) && ($matches[5] == '0' || !empty($matches[5]))) {
							$index = $matches[5];
							$index_type = (isset($matches[6]) && !empty($matches[6])) ? $matches[6] : null;
						}
					
						//add index and group to mapper parameters
						$mapping_params = [$index, $index_type, $group, $group_type];
					}
					else
						$mapping_callback = [$mapper, 'mapResult']; //set default method
				}
				//array mapping type: array, array[], array[column], array[column:type]
				elseif (preg_match(self::ARRAY_TYPE_REGEX, $mapping_type, $matches)) {
					//obtain result map
					$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
						
					//generate a new array mapper object
					$mapper = new ArrayMapper($this->typeManager, $resultMap);
					
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
						}
					
						//obtain index and index type
						if (array_key_exists('callback.index', $this->config)) {
							$index = $this->config['callback.index'];
							$index_type = 'callable';
						}
						elseif (isset($matches[4]) && ($matches[4] == '0' || !empty($matches[4]))) {
							$index = $matches[4];
							$index_type = (isset($matches[5]) && !empty($matches[5])) ? $matches[5] : null;
						}
							
						//add index and group to mapper parameters
						$mapping_params = [$index, $index_type, $group, $group_type];
					}
					else
						$mapping_callback = [$mapper, 'mapResult'];
				}
				//simple mapping type: integer, string, float, etc
				elseif (preg_match(self::SIMPLE_TYPE_REGEX, $mapping_type, $matches)) {					
					//get type handler
					$typeHandler = $this->typeManager->getTypeHandler($matches[1]);
					
					if ($typeHandler === false)
						throw new \InvalidArgumentException("Unknown type '{$matches[1]}'");
						
					//create mapper instance
					$mapper = new ScalarTypeMapper($typeHandler);
						
					//set mapping callback
					$mapping_callback = [$mapper, empty($matches[2]) ? 'mapResult' : 'mapList'];
				}
				else
					throw new \InvalidArgumentException("Unrecognized mapping expression '$mapping_type'");
			}
			else {
				//obtain result map
				$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
					
				//generate mapper
				$mapper = new ArrayMapper($this->typeManager, $resultMap);
					
				//use default mapping type
				$mapping_callback = [$mapper, 'mapList'];
				
				//check for group callback
				if (array_key_exists('callback.group', $this->config)) {
					$group = $this->config['callback.group'];
					$group_type = 'callable';
				}
				else
					$group = $group_type = null;
				
				//check for index callback
				if (array_key_exists('callback.index', $this->config)) {
					$index = $this->config['callback.index'];
					$index_type = 'callable';
				}
				else
					$index = $index_type = null;
				
				$mapping_params = [$index, $index_type, $group, $group_type];
			}
			
			/*
			 * EXECUTE QUERY
			 */
				
			//run query
			$result = $this->driver->query($stmt);
				
			//check query execution
			if ($result === false)
				$this->driver->throwQueryException($stmt);
			
			//check if result is successful
			if ($result === true) {
				//free result
				$this->driver->freeResult($result);
				return true;
			}
			
			/*
			 * INVOKE EMPTY RESULT CALLBACK
			 */
				
			$ri = $this->driver->buildResultIterator($result);
				
			//check if result is empty
			if ($ri->countRows() === 0) {
				if (array_key_exists('callback.empty', $this->config))
					return call_user_func($this->config['callback.empty'], $result);
			}
			else
				$cacheable = true;
			
			/*
			 * ADD CUSTOM MAPPING OPTIONS
			 */
				
			//add defined mapping parameters
			if (array_key_exists('map.params', $this->config)) {
				if (!empty($mapping_params))
					$mapping_params = array_merge($mapping_params, $this->config['map.params']);
				else
					$mapping_params = $this->config['map.params'];
			}
				
			//build mapping callback parameters
			if (isset($mapping_params))
				array_unshift($mapping_params, $ri);
			else
				$mapping_params = [$ri];
			
			/*
			 * MAP RESULT
			 */
				
			//call mapping callback
			$mapped_result = call_user_func_array($mapping_callback, $mapping_params);
				
			//free result
			$this->driver->freeResult($result);
			
			/*
			 * CACHEABLE ATTRIBUTES
			 */
			
			if (isset($resultMap)) {
				$copy = $this->__copy(); //clone current instance
				
				if ($mapping_callback[1] == 'mapList') { //list of rows
					if ($mapper->hasGroupKeys()) { //grouped
						foreach ($mapper->getGroupKeys() as $group) {
							foreach (array_keys($mapped_result[$group]) as $k)
								$mapper->evaluateAttributes($mapped_result[$group][$k], $copy);
						}
					}
					else { //list
						foreach (array_keys($mapped_result) as $k)
							$mapper->evaluateAttributes($mapped_result[$k], $copy);
					}
				}
				else //single row
					$mapper->evaluateAttributes($mapped_result, $copy);
			}
			
			/*
			 * CACHE STORE
			 */
			
			//check if obtained value can be stored
			if (isset($this->cacheProvider) && $cacheable) {
				//build value wrapper
				if ($mapper instanceof ComplexMapper)
					$this->injectCacheMetadata($mapped_result, get_class($mapper), $mapping_callback[1], $mapper->getGroupKeys(), $resultMap);
				
				//store value
				if (array_key_exists('cache.ttl', $this->config))
					$this->cacheProvider->store($cache_key, $mapped_result, intval($this->config['cache.ttl']));
				else
					$this->cacheProvider->store($cache_key, $mapped_result);
			}
		}
		else
			$mapped_result = $cached_value->getValue();
		
		/*
		 * DYNAMIC ATTRIBUTES
		 */
		
		if (isset($resultMap) && $this->config['depth.limit'] > $this->config['depth.current']) {
			$icopy = $this->__icopy();			
			
			if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
				if ($mapper->hasGroupKeys()) {
					foreach ($mapper->getGroupKeys() as $group) {
						foreach (array_keys($mapped_result[$group]) as $k) {
							$mapper->evaluateDynamicAttributes($mapped_result[$group][$k], $icopy);
							$mapper->evaluateAssociations($mapped_result[$group][$k], $icopy);
						}
					}
				}
				else {
					foreach (array_keys($mapped_result) as $k) {
						$mapper->evaluateDynamicAttributes($mapped_result[$k], $icopy);
						$mapper->evaluateAssociations($mapped_result[$k], $icopy);
					}
				}
			}
			elseif ($mapping_callback[1] != 'mapList' && !is_null($mapped_result)) {
				$mapper->evaluateDynamicAttributes($mapped_result, $icopy);
				$mapper->evaluateAssociations($mapped_result, $icopy);
			}
		}
		
		/*
		 * TRAVERSING CALLBACK
		 */
		
		if (array_key_exists('callback.each', $this->config)) {
			$each_callback = $this->config['callback.each'];
			$copy = isset($copy) ? $copy : $this->__copy();
		
			if ($each_callback instanceof \Closure) {
				//check if mapped result is a list
				if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
					if ($mapper instanceof ComplexMapper && $mapper->hasGroupKeys()) {
						foreach ($mapper->getGroupKeys() as $group) {
							foreach (array_keys($mapped_result[$group]) as $k)
								$each_callback->__invoke($mapped_result[$group][$k], $copy);
						}
					}
					else {
						foreach (array_keys($mapped_result) as $k)
							$each_callback->__invoke($mapped_result[$k], $copy);
					}
				}
				elseif (!is_null($mapped_result))
					$each_callback->__invoke($mapped_result, $copy);
			}
			else {
				//this closure avoids getting "expected to be a reference"-style messages
				$c = function (&$mapped_result) use ($each_callback) {
					call_user_func($each_callback, $mapped_result, $copy);
				};
		
				//call traverse callback
				if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
					if ($mapper instanceof ComplexMapper && $mapper->hasGroupKeys()) {
						foreach ($mapper->getGroupKeys() as $group) {
							foreach (array_keys($mapped_result[$group]) as $k)
								$c->__invoke($mapped_result[$group][$k]);
						}
					}
					else {
						foreach (array_keys($mapped_result) as $k)
							$c->__invoke($mapped_result[$k]);
					}
				}
				elseif (!is_null($mapped_result))
					$c->__invoke($mapped_result);
			}
		}
		
		/*
		 * FILTER CALLBACK
		 */
		
		//apply filter
		if (array_key_exists('callback.filter', $this->config)) {
			$filter_callback = $this->config['callback.filter'];
		
			//check if mapped result is a list
			if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
				if ($mapper instanceof ComplexMapper && $mapper->hasGroupKeys()) {
					foreach ($mapper->getGroupKeys() as $group)
						$mapped_result[$key] = array_filter($mapped_result[$group], $filter_callback);
				}
				else
					$mapped_result = array_filter($mapped_result, $filter_callback);
			}
			elseif (!is_null($mapped_result)) {
				if (!call_user_func($filter_callback, $mapped_result))
					$mapped_result = null;
			}
		}
		
		return $mapped_result;
	}
		
	/**
	 * Runs a query
	 * @param string $query
	 * @return mixed
	 */
	public function sql($query) {
		if (!is_string($query) || empty($query))
			throw new \InvalidArgumentException("Query is not a valid string");
	
		//open connection
		$this->driver->connect();
	
		//get query and parameters
		$args = func_get_args();
		$query = array_shift($args);
	
		//build statement
		$stmt = $this->driver->buildStatement($this->typeManager);
	
		//run query
		$result = $this->driver->query($stmt->format($query, $args, $this->config));
	
		//check query execution
		if ($result === false)
			$this->driver->throwQueryException($stmt);
	
		return $result;
	}
	
	/**
	 * Executes a query with the given arguments
	 * @param string $query
	 * @param array $args
	 * @return mixed
	 */
	public function execute($query, $args) {
		array_unshift($args, $query);
		return call_user_func_array([$this, 'query'], $args);
	}
	
	/**
	 * Closes a database connection
	 */
	public function close() {
		return $this->driver->close();
	}
	
	/**
	 * Returns las generated error
	 * @return string
	 */
	public function getLastError() {
		return $this->driver->getLastError();
	}
	
	/**
	 * Returns last generated id
	 * @return int | NULL
	 */
	public function getLastId() {
		return $this->driver->getLastId();
	}
	
	/*
	 * TRANSACTION METHODS
	 */
	
	/**
	 * Begins a transaction
	 */
	public function beginTransaction() {
		if ($this->txStatus == self::TX_NOT_STARTED) {
			$this->txStatus = self::TX_STARTED;
			$this->txCounter = 1;
			return $this->driver->begin();
		}
		
		$this->txCounter++;
		return false;
	}
	
	/**
	 * Commits current transaction
	 */
	public function commit() {
		if ($this->txStatus == self::TX_STARTED && $this->txCounter == 1) {
			$this->txStatus = self::TX_NOT_STARTED;
			$this->txCounter = 0;
			return $this->driver->commit();
		}
		
		$this->txCounter--;
		return false;
	}
	
	/**
	 * Rollbacks current transaction
	 */
	public function rollback() {
		$this->txStatus = self::TX_NOT_STARTED;
		$this->txCounter = 0;
		return $this->driver->rollback();
	}
	
	/**
	 * Obtains database driver
	 * @return \eMapper\Engine\Generic\Driver
	 */
	public function getDriver() {
		return $this->driver;
	}
	
	/**
	 * Obtains type manager
	 * @return \eMapper\Type\TypeManager
	 */
	public function getTypeManager() {
		return $this->typeManager;
	}
	
	/**
	 * Assigns a cache provider to current instance
	 * @param \SimpleCache\CacheProvider $provider
	 */
	public function setCacheProvider(CacheProvider $provider) {
		$this->cacheProvider = $provider;
	}
	
	/**
	 * Obtains assigned cache provider
	 * @return \SimpleCache\CacheProvider
	 */
	public function getCacheProvider() {
		return $this->cacheProvider;
	}
	
	/**
	 * Obtains current transaction status
	 * @return int
	 */
	public function getTransactionStatus() {
		return $this->txStatus;
	}
	
	/*
	 * BUILDER METHODS
	 */
	
	/**
	 * Returns a new StoredProcedure instance
	 * @param string $name
	 * @return \eMapper\Procedure\StoredProcedure
	 */
	public function newProcedureCall($name) {
		return new StoredProcedure($this, $name);
	}
	
	/**
	 * Returns a new FluentQuery instance
	 * @param \eMapper\Reflection\ClassProfile $profile
	 * @return \eMapper\Query\FluentQuery
	 */
	public function newQuery(ClassProfile $profile = null) {
		return new FluentQuery($this, $profile);
	}
	
	/**
	 * Returns a new Manager instance
	 * @param string $classname
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Manager
	 */
	public function newManager($classname) {
		$profile = Profiler::getClassProfile($classname);
		if (!$profile->isEntity())
			throw new \InvalidArgumentException(sprintf("Class %s is not declared as an entity", $profile->reflectionClass->getName()));
		return new Manager($this, $profile);
	}
}