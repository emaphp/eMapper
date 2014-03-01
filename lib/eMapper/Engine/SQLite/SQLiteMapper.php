<?php
namespace eMapper\Engine\SQLite;

use eMapper\Engine\Generic\GenericMapper;
use eMapper\Engine\SQLite\Exception\SQLiteMapperException;
use eMapper\Engine\SQLite\Result\SQLiteResultInterface;
use eMapper\Engine\SQLite\Exception\SQLiteQueryException;
use eMapper\Engine\SQLite\Statement\SQLiteStatement;
use eMapper\Engine\SQLite\Exception\SQLiteConnectionException;
use eMapper\Type\TypeManager;

class SQLiteMapper extends GenericMapper {
	//transaction type constants
	const TX_DEFERRED = 0;
	const TX_IMMEDIATE = 1;
	const TX_EXCLUSIVE = 2;
	
	/**
	 * SQLite database
	 * @var \SQLite3
	 */
	public $db;
	
	/**
	 * Initializes a SQLiteMapper instance
	 * @param mixed $database
	 * @param int $flags
	 * @param string $encription_key
	 * @throws SQLiteMapperException
	 */
	public function __construct($database, $flags = 0, $encription_key = null) {
		if ($database instanceof \SQLite3) {
			$this->db = $database;
		}
		else {
			if (empty($database)) {
				throw new SQLiteMapperException("Filename is not a valid string");
			}
			
			$this->config['db.filename'] = $database;
			
			if (empty($flags)) {
				$flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
			}
			
			$this->config['db.flags'] = $flags;
			$this->config['db.encription_key'] = $encription_key;
		}
		
		//type managet
		$this->typeManager = new TypeManager();
	
		// set default options
		$this->applyDefaultConfig();
	}
	
	/**
	 * Builds a SQLiteMapper instance from a configuration array
	 * @param array $config
	 * @param array $additional_config
	 * @throws \InvalidArgumentException
	 * @return \eMapper\Engine\SQLite\SQLiteMapper
	 */
	public static function build($config, $additional_config = null) {
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
		
		//create instance
		$mapper = new SQLiteMapper($database, $flags, $encription_key);
		
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
	 * Initializes a SQLite database connection
	 * @throws SQLiteConnectionException
	 * @return \SQLite3
	 */
	public function connect() {
		if ($this->db instanceof \SQLite3) {
			return $this->db;
		}
		
		try {
			$this->db = empty($this->config['db.encription_key']) ? new \SQLite3($this->config['db.filename'], $this->config['db.flags']) : new \SQLite3($this->config['db.filename'], $this->config['db.flags'], $this->config['db.encription_key']);
		}
		catch (\Exception $e) {
			throw new SQLiteConnectionException($e->getMessage());
		}
		
		return $this->db;
	}
	
	/**
	 * Frees a SQLite3Result instance
	 * @param \SQLite3Result $result
	 */
	public function free_result($result) {
		if ($result instanceof \SQLite3Result) {
			$result->finalize();
		}
	}
	
	/**
	 * Closes a SQLite database connection
	 */
	public function close() {
		if ($this->db instanceof \SQLite3) {
			$this->db->close();
		}
	}
	
	/**
	 * Obtains last generataded error message
	 */
	public function lastError() {
		if (!($this->db instanceof \SQLite3)) {
			throw new SQLiteMapperException("No valid SQLite database connection available");
		}
		
		return $this->db->lastErrorMsg();
	}
	
	/**
	 * Obtains the last generated ID from a query
	 */
	public function getLastId() {
		if (!($this->db instanceof \SQLite3)) {
			throw new SQLiteMapperException("No valid SQLite database connection available");
		}
		
		return $this->db->lastInsertRowID();
	}
	
	/**
	 * TRANSACTION METHODS
	 */
	
	/**
	 * Starts a transaction
	 * @param int $txType
	 * @return boolean
	 * @throws SQLiteMapperException
	 */
	public function beginTransaction($txType = self::TX_DEFERRED) {
		if (!($this->db instanceof \SQLite3)) {
			throw new SQLiteMapperException("No valid SQLite database connection available");
		}
		
		$type = 'DEFERRED';
		
		if ($txType == self::TX_IMMEDIATE) {
			$type = 'IMMEDIATE';
		}
		elseif ($txType == self::TX_EXCLUSIVE) {
			$type = 'EXCLUSIVE';
		}
		
		return $this->sql("BEGIN $type TRANSACTION");
	}
	
	/**
	 * Commits a transaction
	 * @return boolean
	 * @throws SQLiteMapperException
	 */
	public function commit() {
		if (!($this->db instanceof \SQLite3)) {
			throw new SQLiteMapperException("No valid SQLite database connection available");
		}
		
		return $this->sql("COMMIT");
	}
	
	/**
	 * Rollbacks a transaction
	 * @return boolean
	 * @throws SQLiteMapperException
	 */
	public function rollback() {
		if (!($this->db instanceof \SQLite3)) {
			throw new SQLiteMapperException("No valid SQLite database connection available");
		}
		
		return $this->sql("ROLLBACK");
	}
	
	/**
	 * INTERNAL METHODS
	 */
	
	/**
	 * Wraps a SQLite result using a common interface
	 * @return SQLiteResultInterface
	 */
	protected function build_result_interface($result) {
		return new SQLiteResultInterface($result);
	}
	
	/**
	 * Builds a statement string compatible with SQLite
	 * @return string
	 */
	protected function build_statement($query, $args, $parameterMap) {
		$stmt = new SQLiteStatement($this->db, $this->typeManager, $parameterMap);
		return $stmt->build($query, $args, $this->config);
	}
	
	/**
	 * Sends a query to current SQLite database
	 * @return \SQLite3Result | boolean
	 */
	protected function run_query($query) {
		return $this->db->query($query);
	}
	
	/**
	 * EXCEPTION METHODS
	 */
	
	/**
	 * Throws a SQLiteMapper generic exception
	 */
	public function throw_exception($message) {
		throw new SQLiteMapperException($message);
	}
	
	/**
	 * Throws a SQLite query exception
	 */
	public function throw_query_exception($query) {
		throw new SQLiteQueryException($this->db->lastErrorMsg(), $query);
	}
}
?>