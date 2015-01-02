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
use eMapper\Cache\CacheStorage;
use eMapper\Procedure\StoredProcedure;
use eMapper\Fluent\FluentQuery;
use eMapper\ORM\Manager;
use eMapper\Reflection\ClassProfile;
use SimpleCache\CacheProvider;

/**
 * The Mapper class manages how a database result is converted to a given type.
 * @author emaphp
 */
class Mapper {
	use StatementConfiguration;
	use CacheStorage;
	
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
		$this->config['env.id'] = 'default';
	
		//dynamic sql environment class
		$this->config['env.class'] = 'eMapper\Dynamic\Environment\DynamicSQLEnvironment';
	
		//default relation depth
		$this->config['depth.current'] = 0;
	
		//default relation depth limit
		$this->config['depth.limit'] = 1;
		
		//cache metakey
		$this->config['cache.metakey'] = '__cache__';
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
	 * Returns current database connection
	 * @return object | resorce
	 */
	public function getConnection() {
		return $this->driver->getConnection();
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
	
	/*
	 * CONFIGURATION
	 */
	
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
	 * Configures dynamic sql environment
	 * @param string $id
	 * @param string $class
	 */
	public function setEnvironment($id, $class = 'eMapper\Dynamic\Environment\DynamicSQLEnvironment') {
		$this->config['env.id'] = $id;
		$this->config['env.class'] = $class;
	}
	
	/*
	 * TYPES
	 */
	
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
		
	/*
	 * CACHE
	 */
	
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
	 * Executes a query
	 * @param string $query
	 * @return mixed
	 */
	public function execute($query, $args) {
		/*
		 * INITIALIZE
		 */
		
		//validate query
		if (!is_string($query) || empty($query))
			throw new \InvalidArgumentException("Query is not a valid string");

		//open connection
		$this->driver->connect();
		
		/*
		 * CACHE CONTROL
		 */
		
		$cachedValue = $resultMap = null;
		$cacheable = false; //if mapped value is cacheable
		
		//check if there is a value stored in cache with the given key
		if (array_key_exists('cache.key', $this->config) && isset($this->cacheProvider)) {
			//build cache key
			$cacheKey = (new CacheKeyFormatter($this->typeManager))->format($this->config['cache.key'], $args, $this->config);

			//check if key is present
			if ($this->cacheProvider->exists($cacheKey)) {
				$cachedValue = $this->cacheProvider->fetch($cacheKey);
				
				if (is_array($cachedValue) || is_object($cachedValue)) {
					//get cache metadata
					$cacheMeta = $this->extractCacheMetadata($cachedValue);
					
					//no metadata, we didn't stored it
					if (is_null($cacheMeta)) 
						return $cachedValue;
					
					//create mapper instance
					$mapper = (new \ReflectionClass($cacheMeta->class))->newInstance($this->driver->buildTypeManager(), $cacheMeta->resultMap);
					
					if (!empty($cacheMeta->groups))
						$mapper->setGroupKeys($cacheMeta->groups);
					
					//build mapping callback (method tells if this is a list or a single row)
					$mappingCallback = [$mapper, $cacheMeta->method];
					
					//set resultmap (allows further processing)
					$resultMap = $cacheMeta->resultMap;
				}
				else
					return $cachedValue;
			}
		}
		
		if (is_null($cachedValue)) {
			/*
			 * GENERATE QUERY
			 */
				
			//build statement
			$stmt = $this->driver->buildStatement($this->typeManager)->format($query, $args, $this->config);
			
			//debug query
			if (array_key_exists('callback.debug', $this->config)) 
				call_user_func($this->config['callback.debug'], $stmt);
			
			/*
			 * PARSE MAPPING EXPRESSION
			 */
	
			//build mapping callback
			if (array_key_exists('map.type', $this->config)) {
				//get mapping type
				$mappingType = $this->config['map.type'];
				
				//object mapping type: object, object:class, object[column], object[column:type], etc
				if (preg_match(self::OBJECT_TYPE_REGEX, $mappingType, $matches)) {
					//get class, if any
					if (empty($matches[1])) { //no class defined, use default
						$defaultClass = 'stdClass';
						$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null; //obtain result map
						$mapper = new StdClassMapper($this->typeManager, $resultMap);
					}
					else {
						//remove leading ':'
						$defaultClass = strtolower($matches[1]);
						$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
						
						//get mapping class
						if ($defaultClass == 'stdclass')
							$mapper = new StdClassMapper($this->typeManager, $resultMap);
						elseif ($defaultClass == 'arrayobject')
							$mapper = new ArrayObjectMapper($this->typeManager, $resultMap);
						elseif (Profiler::getClassProfile($matches[1])->isEntity()) { //check if defined class is an entity
							$mapper = new EntityMapper($this->typeManager, $matches[1]);
							$resultMap = $matches[1];
						}
						else {
							$mapper = new ObjectMapper($this->typeManager, $matches[1]);
							$resultMap = null;
						}
					}
					
					//generate a new object mapper object
					$group = $groupType = $index = $indexType = null;
					
					if (count($matches) > 2) {
						$mappingCallback = [$mapper, 'mapList'];
					
						//obtain group
						if (array_key_exists('callback.group', $this->config)) {
							$group = $this->config['callback.group'];
							$groupType = 'callable';
						}
						elseif (isset($matches[2]) && ($matches[2] == '0' || !empty($matches[2]))) {
							$group = $matches[2];
							$groupType = (isset($matches[3]) && !empty($matches[3])) ? $matches[3] : null;
						}
					
						//obtain index
						if (array_key_exists('callback.index', $this->config)) {
							$index = $this->config['callback.index'];
							$indexType = 'callable';
						}
						elseif (isset($matches[5]) && ($matches[5] == '0' || !empty($matches[5]))) {
							$index = $matches[5];
							$indexType = (isset($matches[6]) && !empty($matches[6])) ? $matches[6] : null;
						}
					
						//add index and group to mapper parameters
						$mappingParams = [$index, $indexType, $group, $groupType];
					}
					else
						$mappingCallback = [$mapper, 'mapResult']; //set default method
				}
				//array mapping type: array, array[], array[column], array[column:type]
				elseif (preg_match(self::ARRAY_TYPE_REGEX, $mappingType, $matches)) {
					//obtain result map
					$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
						
					//generate a new array mapper object
					$mapper = new ArrayMapper($this->typeManager, $resultMap);
					
					if (count($matches) > 1) {
						$mappingCallback = [$mapper, 'mapList'];
						$group = $groupType = $index = $indexType = null;
					
						//obtain group and group type
						if (array_key_exists('callback.group', $this->config)) {
							$group = $this->config['callback.group'];
							$groupType = 'callable';
						}
						elseif (isset($matches[1]) && ($matches[1] == '0' || !empty($matches[1]))) {
							$group = $matches[1];
							$groupType = (isset($matches[2]) && !empty($matches[2])) ? $matches[2] : null;
						}
					
						//obtain index and index type
						if (array_key_exists('callback.index', $this->config)) {
							$index = $this->config['callback.index'];
							$indexType = 'callable';
						}
						elseif (isset($matches[4]) && ($matches[4] == '0' || !empty($matches[4]))) {
							$index = $matches[4];
							$indexType = (isset($matches[5]) && !empty($matches[5])) ? $matches[5] : null;
						}
							
						//add index and group to mapper parameters
						$mappingParams = [$index, $indexType, $group, $groupType];
					}
					else
						$mappingCallback = [$mapper, 'mapResult'];
				}
				//simple mapping type: integer, string, float, etc
				elseif (preg_match(self::SIMPLE_TYPE_REGEX, $mappingType, $matches)) {					
					//get type handler
					$typeHandler = $this->typeManager->getTypeHandler($matches[1]);
					
					if ($typeHandler === false)
						throw new \InvalidArgumentException("Unknown type '{$matches[1]}'");
						
					//create mapper instance
					$mapper = new ScalarTypeMapper($typeHandler);
						
					//set mapping callback
					$mappingCallback = [$mapper, empty($matches[2]) ? 'mapResult' : 'mapList'];
				}
				else
					throw new \InvalidArgumentException("Unrecognized mapping expression '$mappingType'");
			}
			else {
				//obtain result map
				$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
					
				//generate mapper
				$mapper = new ArrayMapper($this->typeManager, $resultMap);
					
				//use default mapping type
				$mappingCallback = [$mapper, 'mapList'];
				
				//check for group callback
				if (array_key_exists('callback.group', $this->config)) {
					$group = $this->config['callback.group'];
					$groupType = 'callable';
				}
				else
					$group = $groupType = null;
				
				//check for index callback
				if (array_key_exists('callback.index', $this->config)) {
					$index = $this->config['callback.index'];
					$indexType = 'callable';
				}
				else
					$index = $indexType = null;
				
				$mappingParams = [$index, $indexType, $group, $groupType];
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
				if (!empty($mappingParams))
					$mappingParams = array_merge($mappingParams, $this->config['map.params']);
				else
					$mappingParams = $this->config['map.params'];
			}
				
			//build mapping callback parameters
			if (isset($mappingParams))
				array_unshift($mappingParams, $ri);
			else
				$mappingParams = [$ri];
			
			/*
			 * MAP RESULT
			 */
				
			//call mapping callback
			$mappedResult = call_user_func_array($mappingCallback, $mappingParams);
				
			//free result
			$this->driver->freeResult($result);
			
			/*
			 * CACHEABLE ATTRIBUTES
			 */
			
			if (isset($resultMap)) {
				$copy = $this->__copy(); //clone current instance
				
				if ($mappingCallback[1] == 'mapList') { //list of rows
					if ($mapper->hasGroupKeys()) { //grouped
						foreach ($mapper->getGroupKeys() as $group) {
							foreach (array_keys($mappedResult[$group]) as $k)
								$mapper->evaluateAttributes($mappedResult[$group][$k], $copy);
						}
					}
					else { //list
						foreach (array_keys($mappedResult) as $k)
							$mapper->evaluateAttributes($mappedResult[$k], $copy);
					}
				}
				else //single row
					$mapper->evaluateAttributes($mappedResult, $copy);
			}
			
			/*
			 * CACHE STORE
			 */
			
			//check if obtained value can be stored
			if (isset($this->cacheProvider) && $cacheable) {
				//build value wrapper
				if ($mapper instanceof ComplexMapper)
					$this->injectCacheMetadata($mappedResult, get_class($mapper), $mappingCallback[1], $mapper->getGroupKeys(), $resultMap);
				
				//store value
				if (array_key_exists('cache.ttl', $this->config))
					$this->cacheProvider->store($cacheKey, $mappedResult, intval($this->config['cache.ttl']));
				else
					$this->cacheProvider->store($cacheKey, $mappedResult);
			}
		}
		
		/*
		 * DYNAMIC ATTRIBUTES
		 */
		
		if (!empty($resultMap) && $this->config['depth.limit'] > $this->config['depth.current']) {
			$icopy = $this->__icopy();			
			
			if ($mappingCallback[1] == 'mapList' && !empty($mappedResult)) {
				if ($mapper->hasGroupKeys()) {
					foreach ($mapper->getGroupKeys() as $group) {
						foreach (array_keys($mappedResult[$group]) as $k) {
							$mapper->evaluateDynamicAttributes($mappedResult[$group][$k], $icopy);
							$mapper->evaluateAssociations($mappedResult[$group][$k], $icopy);
						}
					}
				}
				else {
					foreach (array_keys($mappedResult) as $k) {
						$mapper->evaluateDynamicAttributes($mappedResult[$k], $icopy);
						$mapper->evaluateAssociations($mappedResult[$k], $icopy);
					}
				}
			}
			elseif ($mappingCallback[1] != 'mapList' && !is_null($mappedResult)) {
				$mapper->evaluateDynamicAttributes($mappedResult, $icopy);
				$mapper->evaluateAssociations($mappedResult, $icopy);
			}
		}
		
		/*
		 * TRAVERSING CALLBACK
		 */
		
		if (array_key_exists('callback.each', $this->config)) {
			$eachCallback = $this->config['callback.each'];
			$copy = isset($copy) ? $copy : $this->__copy();
		
			if ($eachCallback instanceof \Closure) {
				//check if mapped result is a list
				if ($mappingCallback[1] == 'mapList' && !empty($mappedResult)) {
					if ($mapper instanceof ComplexMapper && $mapper->hasGroupKeys()) {
						foreach ($mapper->getGroupKeys() as $group) {
							foreach (array_keys($mappedResult[$group]) as $k)
								$eachCallback->__invoke($mappedResult[$group][$k], $copy);
						}
					}
					else {
						foreach (array_keys($mappedResult) as $k)
							$eachCallback->__invoke($mappedResult[$k], $copy);
					}
				}
				elseif (!is_null($mappedResult))
					$eachCallback->__invoke($mappedResult, $copy);
			}
			else {
				//this closure avoids getting "expected to be a reference"-style messages
				$c = function (&$mappedResult) use ($eachCallback) {
					call_user_func($eachCallback, $mappedResult, $copy);
				};
		
				//call traverse callback
				if ($mappingCallback[1] == 'mapList' && !empty($mappedResult)) {
					if ($mapper instanceof ComplexMapper && $mapper->hasGroupKeys()) {
						foreach ($mapper->getGroupKeys() as $group) {
							foreach (array_keys($mappedResult[$group]) as $k)
								$c->__invoke($mappedResult[$group][$k]);
						}
					}
					else {
						foreach (array_keys($mappedResult) as $k)
							$c->__invoke($mappedResult[$k]);
					}
				}
				elseif (!is_null($mappedResult))
					$c->__invoke($mappedResult);
			}
		}
		
		/*
		 * FILTER CALLBACK
		 */
		
		//apply filter
		if (array_key_exists('callback.filter', $this->config)) {
			$filterCallback = $this->config['callback.filter'];
		
			//check if mapped result is a list
			if ($mappingCallback[1] == 'mapList' && !empty($mappedResult)) {
				if ($mapper instanceof ComplexMapper && $mapper->hasGroupKeys()) {
					foreach ($mapper->getGroupKeys() as $group)
						$mappedResult[$group] = array_filter($mappedResult[$group], $filterCallback);
				}
				else
					$mappedResult = array_filter($mappedResult, $filterCallback);
			}
			elseif (!is_null($mappedResult)) {
				if (!call_user_func($filterCallback, $mappedResult))
					$mappedResult = null;
			}
		}
		
		return $mappedResult;
	}
		
	/**
	 * Runs a query
	 * @param string $query
	 * @return mixed
	 */
	public function sql($query, $args = []) {
		if (!is_string($query) || empty($query))
			throw new \InvalidArgumentException("Query is not a valid string");
	
		//open connection
		$this->driver->connect();
			
		//build statement
		$stmt = $this->driver->buildStatement($this->typeManager);
	
		//build query
		$query = $stmt->format($query, $args, $this->config);
		
		//invoke debug callback
		if ($this->hasOption('callback.debug')) {
			$callback = $this->getOption('callback.debug');
			if ($callback instanceof \Closure)
				$callback->__invoke($query);
		}
		
		//run query
		$result = $this->driver->query($query);
	
		//check query execution
		if ($result === false)
			$this->driver->throwQueryException($stmt);
	
		return $result;
	}
	
	/**
	 * Executes a query with the given arguments
	 * @param string $query
	 * @return mixed
	 */
	public function query($query) {
		$args = func_get_args();
		$query = array_shift($args);
		return $this->execute($query, $args);
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
	 * CONNECTION METHODS
	 */
	
	/**
	 * Initializes a database connection
	 */
	public function connect() {
		return $this->driver->connect();
	}
	
	/**
	 * Closes a database connection
	 */
	public function close() {
		return $this->driver->close();
	}
	
	/**
	 * Begins a transaction
	 */
	public function beginTransaction() {
		$this->connect();
		
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
	 * @return \eMapper\Fluent\FluentQuery
	 */
	public function newQuery(ClassProfile $profile = null) {
		return new FluentQuery($this, $profile);
	}
	
	/**
	 * Returns a new Manager instance
	 * @param string $classname
	 * @throws \InvalidArgumentException
	 * @return \eMapper\ORM\Manager
	 */
	public function newManager($classname) {
		$profile = Profiler::getClassProfile($classname);
		if (!$profile->isEntity())
			throw new \InvalidArgumentException(sprintf("Class %s is not declared as an entity", $profile->reflectionClass->getName()));
		return new Manager($this, $profile);
	}
}