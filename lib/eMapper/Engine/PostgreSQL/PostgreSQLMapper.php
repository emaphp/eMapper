<?php
namespace eMapper\Engine\PostgreSQL;

use eMapper\Engine\Generic\GenericMapper;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultInterface;
use eMapper\Engine\PostgreSQL\Statement\PostgreSQLStatement;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLMapperException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLQueryException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLConnectionException;
use eMapper\Engine\PostgreSQL\Type\PostgreSQLTypeManager;

class PostgreSQLMapper extends GenericMapper {
	/**
	 * PostgreSQL connection
	 * @var resource
	 */
	public $connection;
	
	/**
	 * Last obtained ID
	 * @var string
	 */
	public $last_id;
	
	/**
	 * Initializes a PostgreSQLMapper instance
	 * @param mixed $database
	 * @param int $connect_type
	 * @throws PostgreSQLMapperException
	 */
	public function __construct($database, $connect_type = null) {
		if (is_resource($database)) {
			$this->connection = $database;
		}
		else {
			if (empty($database)) {
				throw new PostgreSQLMapperException("Connection string is not a valid string");
			}
			
			$this->config['db.connection_string'] = $database;
			
			if (!empty($connect_type)) {
				$this->config['db.connect_type'] = $connect_type;
			}
		}
		
		//type manager
		$this->typeManager = new PostgreSQLTypeManager();
		
		//set default configuration
		$this->applyDefaultConfig();
	}
	
	/**
	 * Builds a PostgreSQLMapper instance from a configuration array
	 * @param array $config
	 * @param array $additional_config
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Engine\PostgreSQL\PostgreSQLMapper
	 */
	public static function build($config, $additional_config = null) {
		if (!is_array($config)) {
			throw new \InvalidArgumentException("Static method 'build' expects an array as first argument");
		}
		
		$conn_string = '';
		
		//validate database name
		if (!array_key_exists('database', $config) && !empty($config['host'])) {
			throw new \InvalidArgumentException("Configuration value 'database' not found");
		}
		
		$conn_string .= sprintf('dbname=%s ', $config['database']);
		
		//add host name
		if (array_key_exists('host', $config) && !empty($config['host'])) {
			$conn_string .= sprintf('host=%s ', $config['host']);
		}
		
		//add port
		if (array_key_exists('port', $config) && !empty($config['port'])) {
			$conn_string .= sprintf('port=%s ', $config['port']);
		}
		
		//add user
		if (array_key_exists('username', $config) && !empty($config['username'])) {
			$conn_string .= sprintf('user=%s ', $config['username']);
		}
		
		//add password
		if (array_key_exists('password', $config)) {
			$conn_string .= sprintf('password=%s ', $config['password']);
		}
		
		//add timeout
		if (array_key_exists('timeout', $config) && !empty($config['timeout'])) {
			$conn_string .= sprintf('connection_timeout=%s ', $config['timeout']);
		}
		
		//add charset
		if (array_key_exists('charset', $config) && !empty($config['charset'])) {
			$conn_string .= sprintf("options='--client_encoding=%s' ", strtoupper(addcslashes($config['charset'], "'\\")));
		}
		
		$conn_string = trim($conn_string);
		
		//build instance
		$mapper = new PostgreSQLMapper($conn_string);
		
		//set database prefix (when available)
		if (array_key_exists('prefix', $config)) {
			$mapper->config['db.prefix'] = $config['prefix'];
		}
		
		if (is_array($additional_config) && !empty($additional_config)) {
			$mapper->config = array_merge($mapper->config, $additional_config);
		}
		
		return $mapper;
	} 
	
	/**
	 * Initializes a PostgreSQL database connection
	 * @throws PostgreSQLConnectionException
	 * @return resource
	 */
	public function connect() {
		if (is_resource($this->connection)) {
			return $this->connection;
		}
		
		$this->connection = empty($this->config['db.connect_type']) ? @pg_connect($this->config['db.connection_string']) : @pg_connect($this->config['db.connection_string'], $this->config['db.connect_type']);
		
		if ($this->connection === false) {
			throw new PostgreSQLConnectionException("Failed to connect to PostgreSQL database. Connection string: " . $this->config['db.connection_string']);
		}
		
		return $this->connection;
	}
	
	
	/**
	 * Frees a PostgreSQL result instance
	 * @param resource $result
	 */
	public function free_result($result) {
		if (is_resource($result)) {
			pg_free_result($result);
		}
	}
	
	/**
	 * Closes a PostgreSQL database connection
	 */
	public function close() {
		if (is_resource($this->connection)) {
			pg_close($this->connection);
		}
	}
	
	/**
	 * Obtains last generataded error message
	 */
	public function lastError() {
		if (!is_resource($this->connection)) {
			throw new PostgreSQLMapperException("No valid PostgreSQL connection available");
		}
		
		return pg_last_error($this->connection);
	}
	
	/**
	 * Obtains the last generated ID from a query
	 */
	public function getLastId() {
		return $this->last_id;
	}
	
	/**
	 * TRANSACTION METHODS
	 */
	
	/**
	 * Begins a transaction
	 * @return boolean
	 * @throws PostgreSQLMapperException
	 */
	public function beginTransaction() {
		if (!is_resource($this->connection)) {
			throw new PostgreSQLMapperException("No valid PostgreSQL connection available");
		}
		
		return $this->sql('BEGIN');
	}
	
	/**
	 * Commits current transaction
	 * @return boolean
	 * @throws PostgreSQLMapperException
	 */
	public function commit() {
		if (!is_resource($this->connection)) {
			throw new PostgreSQLMapperException("No valid PostgreSQL connection available");
		}
		
		return $this->sql('COMMIT');
	}
	
	/**
	 * Rollbacks a transaction
	 * @return boolean
	 * @throws PostgreSQLMapperException
	 */
	public function rollback() {
		if (!is_resource($this->connection)) {
			throw new PostgreSQLMapperException("No valid PostgreSQL connection available");
		}
		
		return $this->sql('ROLLBACK');
	}
	
	/**
	 * INTERNAL METHODS
	 */
	
	/**
	 * Wraps a PostgreSQL result using a common interface
	 * @return string
	 */
	protected function build_statement($query, $args, $parameterMap) {
		$stmt = new PostgreSQLStatement($this->connection, $typeManager, $parameterMap);
		return $stmt->build($query, $args, $this->config);
	}
	
	/**
	 * Builds a statement string compatible with PostgreSQL
	 * @return PostgreSQLResultInterface
	 */
	protected function build_result_interface($result) {
		return new PostgreSQLResultInterface($result);
	}
	
	/**
	 * Runs a query ans returns the result
	 * @return resource | boolean
	 */
	public function run_query($query) {
		$result = pg_query($this->connection, $query);
		
		if (is_resource($result)) {
			$this->last_id = pg_last_oid($result);
		}
		
		return $result;
	}
	
	/**
	 * EXCEPTION METHODS
	 */
	
	/**
	 * Throws a PostgreSQL generic exception
	 */
	public function throw_exception($message, \Exception $previous = null) {
		throw new PostgreSQLMapperException($message, $previous);
	}
	
	/**
	 * Throws a PostgreSQL query exception
	 */
	public function throw_query_exception($query) {
		throw new PostgreSQLQueryException(pg_last_error($this->connection), $query);
	}
}
?>