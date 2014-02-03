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
	 */
	
	public static function build($db_config, $additional_config = null) {
		
	}
	
	public static function buildFromConnection($conn, $additional_config = null) {
		if (!($conn instanceof \mysqli)) {
			throw new \InvalidArgumentException("An instance of mysqli was expected");
		}
		
		//get current database name
		$result = $conn->query("SELECT DATABASE()");
		$row = $result->fetch_array();
		$db_name = $row[0];
		
		//initialize mapper
		$mapper = new MySQLMapper($db_name);
		$mapper->connection = $conn;
		$mapper->free_result($result);
		
		if (!is_null($additional_config)) {
			if (!is_array($additional_config)) {
				throw new \InvalidArgumentException("Additional config must be defined as an array");
			}
			
			return $mapper->config = array_merge($mapper->config, $additional_config);
		}
		
		return $mapper;
	}
	
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
		return $this->connection = $mysqli;
	}
	
	
	
	/**
	 * Runs a query ans returns the result
	 * (non-PHPdoc)
	 * @see \eMapper\Engine\Generic\GenericMapper::run_query()
	 */
	public function run_query($query) {
		return $this->connection->query($query);
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
	
	protected function build_result_interface($result) {
		return new MySQLResultInterface($result);
	}
	
	protected function build_statement($query, $args, $parameterMap) {
		//build statement
		$stmt = new MySQLStatement($this->connection, $this->typeManager, $parameterMap);
		return $stmt->build($query, $args, $this->config);
	}
	
	/**
	 * EXCEPTION METHODS
	 */
	
	public function throw_exception($message) {
		throw new MySQLMapperException($message);
	}
	
	public function throw_query_exception($query) {
		throw new MySQLQueryException(mysqli_error($this->connection), $query);
	}
}
?>