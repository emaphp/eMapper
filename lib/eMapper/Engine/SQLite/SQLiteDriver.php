<?php
namespace eMapper\Engine\SQLite;

use eMapper\Engine\Generic\Driver;
use eMapper\Engine\SQLite\Type\SQLiteTypeManager;
use eMapper\Engine\SQLite\Statement\SQLiteStatement;
use eMapper\Engine\SQLite\Result\SQLiteResultIterator;
use eMapper\Engine\SQLite\Exception\SQLiteQueryException;
use eMapper\Engine\SQLite\Exception\SQLiteException;
use eMapper\Engine\SQLite\Exception\SQLiteConnectionException;

class SQLiteDriver extends Driver {
	public function __construct($database, $flags = 0, $encription_key = null) {
		if ($database instanceof \SQLite3) {
			$this->connection = $database;
		}
		else {
			if (empty($database)) {
				throw new \InvalidArgumentException("Filename is not a valid string");
			}
				
			$this->config['db.filename'] = $database;
				
			if (empty($flags)) {
				$flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
			}
				
			$this->config['db.flags'] = $flags;
			$this->config['db.encription_key'] = $encription_key;
		}
	}
	
	public static function build($config) {
		if (!is_array($config)) {
			throw new \InvalidArgumentException("Static method 'build' expects an array as first argument");
		}
		
		//validate database filename
		if (!array_key_exists('database', $config)) {
			throw new \InvalidArgumentException("Configuration value 'database' not found");
		}
		
		//get configuration values
		$database = $config['database'];
		$flags = array_key_exists('flags', $config) ? $config['flags'] : 0;
		$encription_key = array_key_exists('encription_key', $config) ? $config['encription_key'] : null;
		
		return new static($database, $flags, $encription_key);
	}
	
	/*
	 * CONNECTION METHODS
	 */
	
	public function connect() {
		if ($this->connection instanceof \SQLite3) {
			return $this->connection;
		}
	
		try {
			$this->connection = empty($this->config['db.encription_key']) ? new \SQLite3($this->config['db.filename'], $this->config['db.flags']) : new \SQLite3($this->config['db.filename'], $this->config['db.flags'], $this->config['db.encription_key']);
		}
		catch (\Exception $e) {
			throw new SQLiteConnectionException($e->getMessage(), $e);
		}
	
		return $this->connection;
	}
	
	public function query($query) {
		return $this->connection->query($query);
	}
	
	public function free_result($result) {
		if ($result instanceof \SQLite3Result) {
			$result->finalize();
		}
	}
	
	public function close() {
		if ($this->connection instanceof \SQLite3) {
			return $this->connection->close();
		}
	}
	
	public function get_last_error() {
		if (!($this->connection instanceof \SQLite3)) {
			throw new SQLiteException("No valid SQLite database connection available");
		}
	
		return $this->connection->lastErrorMsg();
	}
	
	public function get_last_id() {
		if (!($this->connection instanceof \SQLite3)) {
			throw new SQLiteException("No valid SQLite database connection available");
		}
	
		return $this->connection->lastInsertRowID();
	}
	
	/*
	 * TRANSACTION METHODS
	 */
	
	public function begin() {
		if (!($this->connection instanceof \SQLite3)) {
			throw new SQLiteException("No valid SQLite database connection available");
		}
	
		return $this->query("BEGIN TRANSACTION");
	}
	
	public function commit() {
		if (!($this->connection instanceof \SQLite3)) {
			throw new SQLiteException("No valid SQLite database connection available");
		}
	
		return $this->query("COMMIT");
	}
	
	public function rollback() {
		if (!($this->connection instanceof \SQLite3)) {
			throw new SQLiteException("No valid SQLite database connection available");
		}
	
		return $this->query("ROLLBACK");
	}
	
	/*
	 * BUILDER METHODS
	 */
	
	public function build_type_manager() {
		return new SQLiteTypeManager();
	}
	
	public function build_statement($typeManager, $parameterMap) {
		return new SQLiteStatement($this->connection, $typeManager, $parameterMap);
	}
	
	public function build_result_iterator($result) {
		return new SQLiteResultIterator($result);
	}
	
	public function build_call($procedure, $tokens, $config) {
		throw new SQLiteException("SQLite driver does not support stored procedures");
	}
	
	/*
	 * EXCEPTION METHODS
	 */
	
	public function throw_exception($message, \Exception $previous = null) {
		throw new SQLiteException($message, $previous);
	}
	
	public function throw_query_exception($query) {
		throw new SQLiteQueryException($this->connection->lastErrorMsg(), $query);
	}
	
	/*
	 * SQL PREDICATES
	 */
	
	public function regex_expression($column, $expression) {
		return sprintf("%s REGEXP '%s'". $column, $this->connection->escapeString($expression));
	}
	
	public function iregex_expression($column, $expression) {
		return sprintf("%s REGEXP '(?i)%s'". $column, $this->connection->escapeString(strtolower($expression)));
	}
}
?>