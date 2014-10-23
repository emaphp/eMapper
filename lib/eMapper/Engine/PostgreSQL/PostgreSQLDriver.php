<?php
namespace eMapper\Engine\PostgreSQL;

use eMapper\Engine\Generic\Driver;
use eMapper\Engine\PostgreSQL\Type\PostgreSQLTypeManager;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;
use eMapper\Engine\PostgreSQL\Statement\PostgreSQLStatement;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLConnectionException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLQueryException;
use eMapper\Engine\PostgreSQL\Regex\PostgreSQLRegex;

/**
 * The PostgreSQLDriver class provides access to a PostgreSQL database engine.
 * @author emaphp
 */
class PostgreSQLDriver extends Driver {
	/**
	 * Last obtained ID
	 * @var string
	 */
	protected $last_id;
	
	public function __construct($database, $connect_type = null) {
		if (is_resource($database))
			$this->connection = $database;
		else {
			if (empty($database))
				throw new \InvalidArgumentException("Connection string is not a valid string");
				
			$this->config['db.connection_string'] = $database;
				
			if (!empty($connect_type))
				$this->config['db.connect_type'] = $connect_type;
		}
		
		$this->regex = new PostgreSQLRegex();
	}
	
	public static function build($config) {
		if (!is_array($config))
			throw new \InvalidArgumentException("Static method 'build' expects an array as first argument");
		
		$conn_string = '';
		
		//validate database name
		if (!array_key_exists('database', $config) || empty($config['database']))
			throw new \InvalidArgumentException("Configuration value 'database' not found");
		
		$conn_string .= sprintf('dbname=%s ', $config['database']);
		
		//add host name
		if (array_key_exists('host', $config) && !empty($config['host']))
			$conn_string .= sprintf('host=%s ', $config['host']);
		
		//add port
		if (array_key_exists('port', $config) && !empty($config['port']))
			$conn_string .= sprintf('port=%s ', $config['port']);
		
		//add user
		if (array_key_exists('username', $config) && !empty($config['username']))
			$conn_string .= sprintf('user=%s ', $config['username']);
		
		//add password
		if (array_key_exists('password', $config))
			$conn_string .= sprintf('password=%s ', $config['password']);
		
		//add timeout
		if (array_key_exists('timeout', $config) && !empty($config['timeout']))
			$conn_string .= sprintf('connection_timeout=%s ', $config['timeout']);
		
		//add charset
		if (array_key_exists('charset', $config) && !empty($config['charset']))
			$conn_string .= sprintf("options='--client_encoding=%s' ", strtoupper(addcslashes($config['charset'], "'\\")));
		
		return new static(trim($conn_string));
	}
	
	/*
	 * CONNECTION METHODS
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
	
	public function query($query) {
		$result = pg_query($this->connection, $query);
	
		if (is_resource($result)) {
			$this->last_id = pg_last_oid($result);
		}
	
		return $result;
	}
	
	public function free_result($result) {
		if (is_resource($result)) {
			pg_free_result($result);
		}
	}
	
	public function close() {
		if (is_resource($this->connection)) {
			pg_close($this->connection);
		}
	}
	
	public function get_last_error() {
		if (!is_resource($this->connection)) {
			throw new PostgreSQLException("No valid PostgreSQL connection available");
		}
	
		return pg_last_error($this->connection);
	}
	
	public function get_last_id() {
		return $this->last_id;
	}
	
	/*
	 * TRANSACTION METHODS
	 */
	
	public function begin() {
		if (!is_resource($this->connection)) {
			throw new PostgreSQLException("No valid PostgreSQL connection available");
		}
	
		return $this->query('BEGIN');
	}
	
	public function commit() {
		if (!is_resource($this->connection)) {
			throw new PostgreSQLException("No valid PostgreSQL connection available");
		}
	
		return $this->query('COMMIT');
	}
	
	public function rollback() {
		if (!is_resource($this->connection)) {
			throw new PostgreSQLException("No valid PostgreSQL connection available");
		}
	
		return $this->query('ROLLBACK');
	}
	
	/*
	 * BUILDER METHODS
	 */
	public function build_type_manager() {
		return new PostgreSQLTypeManager();
	}
	
	public function build_statement($typeManager, $parameterMap) {
		return new PostgreSQLStatement($this->connection, $typeManager, $parameterMap);
	}
	
	public function build_result_iterator($result) {
		return new PostgreSQLResultIterator($result);
	}
	
	public function build_call($procedure, $tokens, $config) {
		//wrap procedure name
		if (array_key_exists('proc.wrap', $config) && $config['proc.wrap'] === true) {
			$procedure = "\"$procedure\"";
		}
		
		//use returned values as a table
		if (array_key_exists('proc.as_table', $config) && $config['proc.as_table'] === true) {
			return "SELECT * FROM $procedure(" . implode(',', $tokens) . ')';
		}
		
		return "SELECT $procedure(" . implode(',', $tokens) . ')';
	}
	
	/*
	 * EXCEPTION METHODS
	 */
	
	public function throw_exception($message, \Exception $previous = null) {
		throw new PostgreSQLException($message, $previous);
	}
	
	public function throw_query_exception($query) {
		throw new PostgreSQLQueryException(pg_last_error($this->connection), $query);
	}
}
?>