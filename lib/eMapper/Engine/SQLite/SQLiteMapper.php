<?php
namespace eMapper\Engine\SQLite;

use eMapper\Engine\Generic\GenericMapper;
use eMapper\Engine\SQLite\Exception\SQLiteMapperException;
use eMapper\Engine\SQLite\Result\SQLiteResultInterface;
use eMapper\Engine\SQLite\Exception\SQLiteQueryException;
use eMapper\Engine\SQLite\Statement\SQLiteStatement;
use eMapper\Engine\SQLite\Exception\SQLiteConnectionException;

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
	 * @param string $filename
	 * @param int $flags
	 * @param string $encription_key
	 * @throws SQLiteMapperException
	 */
	public function __construct($filename, $flags = 0, $encription_key = null) {
		if (!is_string($filename) || empty($filename)) {
			throw new SQLiteMapperException("Filename is not a valid string");
		}
		
		$this->config['db.filename'] = $filename;
		
		if (empty($flags)) {
			$flags = QLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
		}
		
		$this->config['db.flags'] = $flags;
		$this->config['db.encription_key'] = $encription_key;
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
	 * Sends a query to current SQLite database
	 * @return \SQLite3Result | boolean
	 */
	public function run_query($query) {
		if (!($this->db instanceof \SQLite3)) {
			throw new SQLiteMapperException("No valid database instance found");
		}
		
		return $this->db->query($query);
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
	 * TRANSACTION METHODS
	 */
	
	/**
	 * Starts a transaction
	 * @param int $txType
	 * @return boolean
	 * @throws SQLiteMapperException
	 */
	public function begin_transaction($txType = self::TX_DEFERRED) {
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