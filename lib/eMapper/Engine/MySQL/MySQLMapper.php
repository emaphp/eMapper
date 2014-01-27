<?php
namespace eMapper\Engine\MySQL;

use eMapper\Type\TypeManager;
use eMapper\Cache\CacheProvider;
use eMapper\Cache\Key\CacheKey;
use eMapper\Engine\Generic\GenericMapper;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Result\Mapper\ArrayTypeMapper;
use eMapper\Result\Mapper\ObjectTypeMapper;
use eMapper\Reflection\Profiler;
use eMapper\Engine\MySQL\Configuration\MySQLMapperConfiguration;
use eMapper\Engine\MySQL\Result\MySQLResultInterface;
use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Engine\MySQL\Exception\MySQLMapperException;
use eMapper\Engine\MySQL\Exception\MySQLConnectionException;
use eMapper\Engine\MySQL\Exception\MySQLQueryException;
use eMapper\Result\Mapper\ComplexTypeMapper;

class MySQLMapper extends GenericMapper {
	use MySQLMapperConfiguration;
	
	/**
	 * MySQL connection
	 * @var \mysqli
	 */
	public $connection;
	
	/**
	 * Type manager
	 * @var TypeManager
	 */
	public $typeManager;
	
	public function __construct($database, $host = null, $user = null, $password = null, $port = null, $socket = null, $autocommit = true) {
		if (!is_string($database) || empty($database)) {
			throw new MySQLMapperException("Invalid database specified");
		}
	
		//initialize configuration
		$this->config['db.name'] = $database;
	
		if (isset($host)) {
			if (!is_string($host) || empty($host)) {
				throw new MySQLMapperException("Invalid host specified");
			}
	
			$this->config['db.host'] = $host;
		}
	
		if (isset($user)) {
			if (!is_string($user) || empty($user)) {
				throw new MySQLMapperException("Invalid user specified");
			}
	
			$this->config['db.user'] = $user;
		}
	
		if (isset($password)) {
			if (!is_string($password) || empty($password)) {
				throw new MySQLMapperException("Invalid password specified");
			}
	
			$this->config['db.password'] = $password;
		}
	
		if (isset($port)) {
			if (!is_string($port) || !is_integer($port) || empty($port)) {
				throw new MySQLMapperException("Invalid port specified");
			}
	
			$this->config['db.port'] = (string) $port;
		}
	
		if (isset($socket)) {
			if (!is_string($socket) || empty($socket)) {
				throw new MySQLMapperException("Invalid socket specified");
			}
	
			$this->config['db.socket'] = $socket;
		}
	
		//aet autocommit option
		$this->config['db.autocommit'] = (bool) $autocommit;
	
		//type manager
		$this->typeManager = new TypeManager();
		
		//set default options
		$this->applyDefaultConfig();
	}
	
	/**
	 * TODO: Method build
	 * TODO: Method buildFromConnection
	 */
	
	/**
	 * Creates a new connection to a MySQL server
	 * @throws MySQLMapperException
	 * @return \mysqli
	 */
	protected function connect() {
		//check if connection is already opened
		if ($this->connection instanceof \mysqli) {
			return $this->connection;
		}
	
		//get connection values
		$database = $this->config['db.name'];
		$host     = array_key_exists('db.host', $this->config) ? $this->config['db.host'] : ini_get("mysqli.default_host");
		$user     = array_key_exists('db.user', $this->config) ? $this->config['db.user'] : ini_get("mysqli.default_user");
		$password = array_key_exists('db.password', $this->config) ? $this->config['db.password'] : ini_get("mysqli.default_pw");
		$port     = array_key_exists('db.port', $this->config) ? $this->config['db.port'] : ini_get("mysqli.default_port");
		$socket   = array_key_exists('db.socket', $this->config) ? $this->config['db.socket'] : ini_get("mmysqli.default_socket");
	
		//open connection
		$mysqli = @mysqli_connect($host, $user, $password, $database, $port, $socket);
	
		if (!($mysqli instanceof \mysqli)) {
			throw new MySQLConnectionException(mysqli_connect_error() . '(' . mysqli_connect_errno() . ')');
		}
	
		//set autocommit
		$mysqli->autocommit($this->config['db.autocommit']);
	
		//store open connection
		return $this->connection = $this->config['db.conn'] = $mysqli;
	}
	
	/**
	 * Builds an statement with the given parameters
	 * @param string $query
	 * @param array $args
	 * @return mixed
	 */
	protected function build_statement($query, $args, $parameterMap) {
		//build statement
		$stmtBuilder = new MySQLStatement($this->connection, $this->typeManager, $parameterMap);
		return $stmtBuilder->build($query, $args, $this->config);
	}
	
	/**
	 * Executes a query
	 * @param string $query
	 */
	public function query($query) {
		/**
		 * INITIALIZE
		 */
		
		//validate query
		if (!is_string($query) || empty($query)) {
			throw new MySQLMapperException("Query is not a valid string");
		}
		
		//check current mapper depth
		if ($this->config['depth.current'] >= $this->config['depth.limit']) {
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
		$cacheHandler = $parameterMap = null;
		
		//get parameter map
		if (array_key_exists('map.parameter', $this->config)) {
			$parameterMap = $this->config['map.parameter'];
		}
		elseif (!empty($args) && is_object($args[0]) && Profiler::isEntity(get_class($args[0]))) {
			$parameterMap = get_class($args[0]);
		}
		
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
		 * CACHE CONTROL
		 */
		
		//check if there is a value stored in cache with the given key
		if (array_key_exists('cache.key', $this->config)) {
			//obtain cache provider
			$cacheProvider = $this->config['cache.provider'];

			//build cache key
			$cacheKeyBuilder = new CacheKey($this->typeManager, $parameterMap);
			$cacheKey = $cacheKeyBuilder->build($this->config['cache.key'], $args, $this->config);
				
			//check if key is present
			if ($cacheProvider->exists($cacheKey)) {
				$cache_value = $cacheProvider->fetch($cacheKey);
				
				//TODO: apply relations
				
				return $cache_value;
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
				if (empty($matches[2])) {
					$defaultClass = 'stdClass';
					$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
				}
				else {
					//remove leading ':'
					$defaultClass = substr($matches[2], 1);
					$resultMap = null;

					//get result map
					if (!array_key_exists('map.result', $this->config)) {
						$resultMap = $this->config['map.result'];
					}
					elseif (Profiler::isEntity($defaultClass)) {
						$resultMap = $defaultClass;
					}
				}
			
				//generate a new object mapper object
				$mapper = new ObjectTypeMapper($this->typeManager, $resultMap, $parameterMap, $defaultClass);
			
				if (!empty($matches[3])) {
					//add method
					$mapping_callback = array($mapper, 'mapList');
			
					//check if index type has been defined
					if (!empty($matches[6])) {
						$index = $matches[5];
						$type = substr($matches[6], 1);
							
						//check type specifier
						if (!in_array($type, $this->typeManager->getTypesList())) {
							throw new MySQLMapperException("Unrecognized index type '$type'");
						}
					}
					//check if index has been specified
					elseif (!empty($matches[4])) {
						//get index column
						$index = $matches[4];
						$type = null;
					}
					//no index defined
					else {
						$index = $type = null;
					}
			
					//add index to mapper parameters
					$mapping_params = array($index, $type);
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
			
				if (!empty($matches[2])) {
					$mapping_callback = array($mapper, 'mapList');
			
					//check if index type has been defined
					if (!empty($matches[5])) {
						$index = $matches[4];
						$type = substr($matches[5], 1);
							
						//check type specifier
						if (!in_array($type, $this->typeManager->getTypesList())) {
							throw new MySQLMapperException("Unrecognized index type '$type'");
						}
					}
					//check if index has been specified
					elseif (array_key_exists(3, $matches)) {
						//get index column
						$index = $matches[3];
						$type = null;
					}
					//no index defined
					else {
						$index = $type = null;
					}
			
					//add index to mapper parameters
					$mapping_params = array($index, $type);
				}
				else {
					$mapping_callback = array($mapper, 'mapResult');
				}
			}
			//simple mapping type: integer, string, array, etc
			elseif (preg_match(self::SIMPLE_TYPE_REGEX, $mapping_type, $matches)) {
				//check type
				if (!in_array($matches[1], $this->typeManager->getTypesList())) {
					throw new MySQLMapperException("Unrecognized type '{$matches[1]}'");
				}
			
				//get type handler
				$typeHandler = $this->typeManager->getTypeHandler($matches[1]);
			
				if ($typeHandler === false) {
					throw new MySQLMapperException("Unknown type '{$matches[1]}'");
				}
			
				//set mapping callback
				$mapping_callback = array(new ScalarTypeMapper($typeHandler));
				$mapping_callback[] = empty($matches[2]) ? 'mapResult' : 'mapList';
			}
			else {
				throw new MySQLMapperException("Unrecognized mapping expression '$mapping_type'");
			}
		}
		else {
			//obtain result map
			$resultMap = array_key_exists('map.result', $this->config) ? $this->config['map.result'] : null;
			
			//generate mapper
			$mapper = new ArrayTypeMapper($this->typeManager, $resultMap, $parameterMap);
			
			//use default mapping type
			$mapping_callback = array($mapper, 'mapList');
			$mapping_params = array();
		}
		
		/**
		 * EXECUTE QUERY
		 */
		
		//run query
		$result = $this->connection->query($stmt);
		
		//check query execution
		if ($result === false) {
			throw new MySQLQueryException(mysqli_error($this->connection), $stmt);
		}
		
		//check if result is successful
		if ($result === true) {
			//free result
			$this->free_result($result);
			return true;
		}
		
		$cacheable = true;
		
		/**
		 * INVOKE EMPTY RESULT CALLBACK
		 */
		
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
			array_unshift($mapping_params, new MySQLResultInterface($result));
		}
		else {
			$mapping_params = array(new MySQLResultInterface($result));
		}
		
		/**
		 * MAP RESULT
		 */
		
		//call mapping callback
		$mapped_result = call_user_func_array($mapping_callback, $mapping_params);
		
		
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
		
		/**
		 * EVALUATE RELATIONS
		 */
		
		if (isset($resultMap)) {
			if ($mapping_callback[1] == 'mapList' && !empty($mapped_result)) {
				$keys = array_keys($mapped_result);
				
				foreach ($keys as $k) {
					$mapper->relate($mapped_result[$k], $this);
				}
			}
			elseif (!is_null($mapped_result)) {
				$mapper->relate($mapped_result, $this);
			}
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
					$keys = array_keys($mapped_result);
						
					for ($i = 0, $n = count($keys); $i < $n; $i++) {
						$each_callback->__invoke($mapped_result[$keys[$i]], $new_instance);
					}
				}
				elseif ($mapping_callback[1] == 'mapResult' && !is_null($mapped_result)) {
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
					$keys = array_keys($mapped_result);
		
					for ($i = 0, $n = count($keys); $i < $n; $i++) {
						$c->__invoke($mapped_result[$keys[$i]]);
					}
				}
				elseif ($mapping_callback[1] == 'mapResult' && !is_null($mapped_result)) {
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
				$mapped_result = array_filter($mapped_result, $filter_callback);
			}
			elseif ($mapping_callback[1] == 'mapResult' && !is_null($mapped_result)) {
				if (!call_user_func($filter_callback, $mapped_result)) {
					$mapped_result = null;
				}
			}
		}
		
		//free result
		$this->free_result($result);
		
		return $mapped_result;
	}
	
	/**
	 * Executes a previously declared statement
	 * @param string $statementId
	 * @throws MySQLMapperException
	 * @return mixed
	 */
	public function execute($statementId) {
		//obtain parameters
		$args = func_get_args();
		$statementId = array_shift($args);
	
		if (!is_string($statementId) || empty($statementId)) {
			throw new MySQLMapperException("Statement id must be a valid string");
		}
	
		//obtain statement
		$stmt = $this->getStatement($statementId);
	
		if ($stmt === false) {
			throw new MySQLMapperException("Statement '$statementId' could not be found");
		}
	
		//get statement config
		$query = $stmt->query;
		$options = $stmt->options;
	
		//add query to method parameters
		array_unshift($args, $query);
	
		//call query method
		return (empty($options)) ? call_user_func_array(array($this, 'query'), $args) : call_user_func_array(array($this->merge($options, true), 'query'), $args);
	}
	
	/**
	 * Invokes a stored procedure
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args) {
		//override procedure name
		if (array_key_exists('procedure.use_prefix', $this->config) && $this->config['procedure.use_prefix'] === true) {
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
		if (array_key_exists('procedure.types', $this->config)) {
			$parameter_types = $this->config['procedure.types'];
	
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
	
		//build query
		array_unshift($args, "CALL $procedure_name(" . implode(',', $tokens) . ')');
	
		//call query
		return call_user_func_array(array($this, 'query'), $args);
	}
	
	/**
	 * Runs a query and returns the mysqli_result object
	 * @param string $query
	 * @throws eMapper\Exception\MySQL\MySQLMapperException
	 * @throws eMapper\Exception\NoRowsException
	 * @return \mysqli_result
	 */
	public function sql($query) {
		if (!is_string($query) || empty($query)) {
			throw new MySQLMapperException("Query is not a valid string");
		}
	
		//open connection
		$this->connect();
	
		//get query and parameters
		$args = func_get_args();
		$query = array_shift($args);
	
		//build statement
		$stmt = $this->build_statement($query, $args);
	
		//run query
		$result = $this->connection->query($stmt);
	
		//check query execution
		if ($result === false) {
			throw new MySQLMapperException(sprintf("MySQL query failed: \"%s\"", mysqli_error($this->connection)));
		}
	
		return $result;
	}
	
	/**
	 * Commits current transaction
	 * @return boolean
	 * @throws \eMapper\Exception\MySQL\MySQLMapperException
	 */
	public function commit() {
		if (!($this->connection instanceof \mysqli)) {
			throw new MySQLMapperException("No valid MySQL connection available");
		}
	
		return $this->connection->commit();
	}
	
	/**
	 * Rollbacks current transaction
	 * @return boolean
	 * @throws \eMapper\Exception\MySQL\MySQLMapperException
	 */
	public function rollback() {
		if (!($this->connection instanceof \mysqli)) {
			throw new MySQLMapperException("No valid MySQL connection available");
		}
	
		return $this->connection->rollback();
	}
	
	/**
	 * Escapes a string
	 * @param string $string
	 */
	public function escape($string) {
		//open connection
		$this->connect();
		return $this->connection->real_escape_string($string);
	}
	
	/**
	 * Frees a result
	 * @param \mysqli_result $result
	 */
	public function free_result($result) {
		//free result
		if ($result instanceof \mysqli_result) {
			$result->free();
		}
	
		//free additional results
		while ($this->connection->more_results() && $this->connection->next_result()) {
			$result = $this->connection->use_result();
	
			if ($result instanceof \mysqli_result) {
				$result->free();
			}
		}
	}
	
	/**
	 * Closes connection to a MySQL server
	 */
	public function close() {
		if ($this->connection instanceof \mysqli) {
			$this->connection->close();
		}
	}
}
?>