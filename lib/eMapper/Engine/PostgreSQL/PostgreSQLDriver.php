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
use eMapper\Type\TypeManager;

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
		parent::__construct();
		
		if (is_resource($database))
			$this->connection = $database;
		else {
			if (empty($database))
				throw new \InvalidArgumentException("Connection string is not a valid string");
				
			$this->config['connection_string'] = $database;
				
			if (!empty($connect_type))
				$this->config['connect_type'] = $connect_type;
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
		if (is_resource($this->connection))
			return $this->connection;
	
		$this->connection = empty($this->config['connect_type']) ? @pg_connect($this->config['connection_string']) : @pg_connect($this->config['connection_string'], $this->config['connect_type']);
	
		if ($this->connection === false)
			throw new PostgreSQLConnectionException("Failed to connect to PostgreSQL database. Connection string: " . $this->config['connection_string']);
	
		return $this->connection;
	}
	
	public function query($query) {
		$result = pg_query($this->connection, $query);
	
		if (is_resource($result))
			$this->last_id = pg_last_oid($result);
	
		return $result;
	}
	
	public function freeResult($result) {
		if (is_resource($result))
			pg_free_result($result);
	}
	
	public function close() {
		if (is_resource($this->connection))
			pg_close($this->connection);
	}
	
	public function getLastError() {
		if (!is_resource($this->connection))
			throw new PostgreSQLException("No valid PostgreSQL connection available");
	
		return pg_last_error($this->connection);
	}
	
	public function getLastId() {
		return $this->last_id;
	}
	
	/*
	 * TRANSACTION METHODS
	 */
	
	public function begin() {
		if (!is_resource($this->connection))
			throw new PostgreSQLException("No valid PostgreSQL connection available");
	
		return $this->query('BEGIN');
	}
	
	public function commit() {
		if (!is_resource($this->connection))
			throw new PostgreSQLException("No valid PostgreSQL connection available");
	
		return $this->query('COMMIT');
	}
	
	public function rollback() {
		if (!is_resource($this->connection))
			throw new PostgreSQLException("No valid PostgreSQL connection available");
	
		return $this->query('ROLLBACK');
	}
	
	/*
	 * BUILDER METHODS
	 */
	public function buildTypeManager() {
		return new PostgreSQLTypeManager();
	}
	
	public function buildStatement(TypeManager $typeManager) {
		return new PostgreSQLStatement($this, $typeManager);
	}
	
	public function buildResultIterator($result) {
		return new PostgreSQLResultIterator($result);
	}
	
	public function buildCall($procedure, $tokens, $config) {
		//append prefix
		if ($config['proc.usePrefix'])
			$procedure = $config['proc.prefix'] . $procedure;
		
		//wrap procedure name
		if ($config['proc.escapeName'])
			$procedure = "\"$procedure\"";
		
		//procedure returns a set
		if ($config['proc.returnSet'])
			return "SELECT * FROM $procedure(" . implode(',', $tokens) . ')';
		
		return "SELECT $procedure(" . implode(',', $tokens) . ')';
	}
	
	/*
	 * EXCEPTION METHODS
	 */
	
	public function throwQueryException($query) {
		throw new PostgreSQLQueryException(pg_last_error($this->connection), $query);
	}
}