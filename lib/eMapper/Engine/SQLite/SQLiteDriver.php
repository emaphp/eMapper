<?php
namespace eMapper\Engine\SQLite;

use eMapper\Engine\Generic\Driver;
use eMapper\Engine\SQLite\Type\SQLiteTypeManager;
use eMapper\Engine\SQLite\Statement\SQLiteStatement;
use eMapper\Engine\SQLite\Result\SQLiteResultIterator;
use eMapper\Engine\SQLite\Exception\SQLiteQueryException;
use eMapper\Engine\SQLite\Exception\SQLiteException;
use eMapper\Engine\SQLite\Exception\SQLiteConnectionException;
use eMapper\Engine\SQLite\Regex\SQLiteRegex;

/**
 * The SQLDriver class provides connection to SQLite database engines.
 * @author emaphp
 */
class SQLiteDriver extends Driver {
	public function __construct($database, $flags = 0, $encription_key = null) {
		parent::__construct();
		
		if ($database instanceof \SQLite3)
			$this->connection = $database;
		else {
			if (empty($database))
				throw new \InvalidArgumentException("Filename is not a valid string");
				
			$this->config['filename'] = $database;
				
			if (empty($flags))
				$flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
				
			$this->config['flags'] = $flags;
			$this->config['encription_key'] = $encription_key;
		}
		
		$this->regex = new SQLiteRegex();
	}
	
	public static function build($config) {
		if (!is_array($config))
			throw new \InvalidArgumentException("Static method 'build' expects an array as first argument");
		
		//validate database filename
		if (!array_key_exists('database', $config))
			throw new \InvalidArgumentException("Configuration value 'database' not found");
		
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
		if ($this->connection instanceof \SQLite3)
			return $this->connection;
	
		try {
			$this->connection = empty($this->config['encription_key']) ? new \SQLite3($this->config['filename'], $this->config['flags']) : new \SQLite3($this->config['filename'], $this->config['flags'], $this->config['encription_key']);
			
			//add regexp functions
			$this->connection->createFunction('REGEXP', [$this, 'regexp']);
			$this->connection->createFunction('IREGEXP', [$this, 'iregexp']);
		}
		catch (\Exception $e) {
			throw new SQLiteConnectionException($e->getMessage());
		}
	
		return $this->connection;
	}
	
	public function query($query) {
		return $this->connection->query($query);
	}
	
	public function freeResult($result) {
		if ($result instanceof \SQLite3Result)
			$result->finalize();
	}
	
	public function close() {
		if ($this->connection instanceof \SQLite3)
			return $this->connection->close();
	}
	
	public function getLastError() {
		if (!($this->connection instanceof \SQLite3))
			throw new SQLiteException("No valid SQLite database connection available");
	
		return $this->connection->lastErrorMsg();
	}
	
	public function getLastId() {
		if (!($this->connection instanceof \SQLite3))
			throw new SQLiteException("No valid SQLite database connection available");
	
		return $this->connection->lastInsertRowID();
	}
	
	/*
	 * TRANSACTION METHODS
	 */
	
	public function begin() {
		if (!($this->connection instanceof \SQLite3))
			throw new SQLiteException("No valid SQLite database connection available");
	
		return $this->query("BEGIN TRANSACTION");
	}
	
	public function commit() {
		if (!($this->connection instanceof \SQLite3))
			throw new SQLiteException("No valid SQLite database connection available");
	
		return $this->query("COMMIT");
	}
	
	public function rollback() {
		if (!($this->connection instanceof \SQLite3))
			throw new SQLiteException("No valid SQLite database connection available");
	
		return $this->query("ROLLBACK");
	}
	
	/*
	 * BUILDER METHODS
	 */
	
	public function buildTypeManager() {
		return new SQLiteTypeManager();
	}
	
	public function buildStatement($typeManager) {
		return new SQLiteStatement($this, $typeManager);
	}
	
	public function buildResultIterator($result) {
		return new SQLiteResultIterator($result);
	}
	
	public function buildCall($procedure, $tokens, $options) {
		throw new SQLiteException("SQLite driver does not support stored procedures");
	}
	
	/*
	 * EXCEPTION METHODS
	 */
	
	public function throwException($message, \Exception $previous = null) {
		throw new SQLiteException($message, $previous);
	}
	
	public function throwQueryException($query) {
		throw new SQLiteQueryException($this->connection->lastErrorMsg(), $query);
	}
	
	/*
	 * REGEXP METHODS
	 */
	
	public function regexp($pattern, $string) {
		if (preg_match('/' . addcslashes($pattern, '/') . '/', $string))
        	return true;
    	
    	return false;
	}
	
	public function iregexp($pattern, $stringR) {
		if (preg_match('/' . addcslashes($pattern, '/') . '/i', $string))
			return true;
		 
		return false;
	}
}
?>