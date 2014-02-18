<?php
namespace eMapper\Engine\MySQL;

use eMapper\Type\TypeManager;
use eMapper\Engine\Generic\GenericMapper;
use eMapper\Engine\MySQL\Configuration\MySQLMapperConfiguration;
use eMapper\Engine\MySQL\Result\MySQLResultInterface;
use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Engine\MySQL\Exception\MySQLMapperException;
use eMapper\Engine\MySQL\Exception\MySQLConnectionException;
use eMapper\Engine\MySQL\Exception\MySQLQueryException;

class MySQLMapper extends GenericMapper {
	use MySQLMapperConfiguration;
	
	/**
	 * MySQL connection
	 * @var \mysqli
	 */
	public $connection;
	
	/**
	 * Initializes a MySQLMapper instance
	 * @param string $database
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param string $port
	 * @param string $socket
	 * @param string charset
	 * @param boolean $autocommit
	 * @throws MySQLMapperException
	 */
	public function __construct($database, $host = null, $user = null, $password = null, $port = null, $socket = null, $charset = 'UTF-8', $autocommit = true) {
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
	
			$this->config['db.port'] = strval($port);
		}
	
		if (isset($socket)) {
			if (!is_string($socket) || empty($socket)) {
				throw new MySQLMapperException("Invalid socket specified");
			}
	
			$this->config['db.socket'] = $socket;
		}
		
		if (isset($charset)) {
			if (!is_string($charset) || empty($charset)) {
				throw new MySQLMapperException("Invalid charset specified");
			}
			
			$this->config['db.charset'] = $charset;
		}
	
		//aet autocommit option
		$this->config['db.autocommit'] = (bool) $autocommit;
	
		//type manager
		$this->typeManager = new TypeManager();
		
		//set default options
		$this->applyDefaultConfig();
	}

	/**
	 * Builds a MySQLMapper instance from a configuration array
	 * @param array $config
	 * @param array $additional_config
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Engine\MySQL\MySQLMapper
	 */
	public static function build($config, $additional_config = null) {
		if (!is_array($config)) {
			throw new \InvalidArgumentException("Static method 'build' expects an array as first argument");
		}
		
		//validate database filename
		if (!array_key_exists('database', $config)) {
			throw new \InvalidArgumentException("Configuration value 'database' not found");
		}
		
		$database = $config['database'];
		$host = array_key_exists('host', $config) ? $config['host'] : null;
		$username = array_key_exists('username', $config) ? $config['username'] : null;
		$password = array_key_exists('password', $config) ? $config['password'] : null;
		$port = array_key_exists('port', $config) ? $config['port'] : null;
		$socket = array_key_exists('socket', $config) ? $config['socket'] : null;
		$charset = array_key_exists('charset', $config) ? $config['charset'] : null;
		$autocommit = array_key_exists('autocommit', $config) ? $config['autocommit'] : null;
		
		$mapper = new MySQLMapper($database, $host, $username, $password, $port, $socket, $charset, $autocommit);
		
		//set database prefix (when available)
		if (array_key_exists('prefix', $config)) {
			$mapper->config['db.prefix'] = $config['prefix'];
		}
		
		//merge additional configuration values
		if (is_array($additional_config) && !empty($additional_config)) {
			$mapper->config = array_merge($mapper->config, $additional_config);
		}
		
		return $mapper;
	}
		
	/**
	 * Initializes a MySQL database connection
	 * @throws MySQLConnectionException
	 * @return \mysqli
	 */
	public function connect() {
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
		$socket   = array_key_exists('db.socket', $this->config) ? $this->config['db.socket'] : ini_get("mysqli.default_socket");
	
		//open connection
		$mysqli = @mysqli_connect($host, $user, $password, $database, $port, $socket);
	
		if (!($mysqli instanceof \mysqli)) {
			throw new MySQLConnectionException(mysqli_connect_error() . '(' . mysqli_connect_errno() . ')');
		}
	
		//set autocommit
		$mysqli->autocommit($this->config['db.autocommit']);
		
		//set charset
		if (array_key_exists('db.charset', $this->config)) {
			$mysqli->set_charset($this->config['db.charset']);
		}
	
		//store open connection
		return $this->connection = $mysqli;
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
	 * Frees a MySQL result instance
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
	 * Closes a SQLite database connection
	 */
	public function close() {
		if ($this->connection instanceof \mysqli) {
			$this->connection->close();
		}
	}
	
	/**
	 * Obtains last generataded error message
	 */
	public function lastError() {
		if (!($this->connection instanceof \mysqli)) {
			throw new MySQLMapperException("No valid MySQL connection available");
		}
		
		return mysqli_error($this->connection);
	}
	
	/**
	 * Obtains the last generated ID from a query
	 */
	public function getLastId() {
		if (!($this->connection instanceof \mysqli)) {
			throw new MySQLMapperException("No valid MySQL connection available");
		}
		
		return $this->connection->insert_id;
	}
	
	/**
	 * TRANSACTION METHODS
	 */
	
	/**
	 * Begins a transaction
	 * @return boolean
	 * @throws MySQLMapperException
	 */
	public function beginTransaction() {
		if (!($this->connection instanceof \mysqli)) {
			throw new MySQLMapperException("No valid MySQL connection available");
		}
	
		//warning: PHP 5.5 required
		return $this->connection->begin_transaction();
	}
	
	/**
	 * Commits current transaction
	 * @return boolean
	 * @throws MySQLMapperException
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
	 * @throws MySQLMapperException
	 */
	public function rollback() {
		if (!($this->connection instanceof \mysqli)) {
			throw new MySQLMapperException("No valid MySQL connection available");
		}
	
		return $this->connection->rollback();
	}
	
	/**
	 * INTERNAL METHODS
	 */
	
	/**
	 * Wraps a MySQL result using a common interface
	 * @return MySQLResultInterface
	 */
	protected function build_result_interface($result) {
		return new MySQLResultInterface($result);
	}
	
	/**
	 * Builds a statement string compatible with MySQL
	 * @return string
	 */
	protected function build_statement($query, $args, $parameterMap) {
		//build statement
		$stmt = new MySQLStatement($this->connection, $this->typeManager, $parameterMap);
		return $stmt->build($query, $args, $this->config);
	}
	
	/**
	 * Runs a query ans returns the result
	 * @return \mysqli_result | boolean
	 */
	protected function run_query($query) {
		if (!($this->connection instanceof \mysqli)) {
			throw new MySQLMapperException("No valid MySQL connection available");
		}
	
		return $this->connection->query($query);
	}
	
	/**
	 * EXCEPTION METHODS
	 */
	
	/**
	 * Throws a MySQL generic exception
	 */
	public function throw_exception($message) {
		throw new MySQLMapperException($message);
	}
	
	/**
	 * Throws a MySQL query exception
	 */
	public function throw_query_exception($query) {
		throw new MySQLQueryException(mysqli_error($this->connection), $query);
	}
}
?>