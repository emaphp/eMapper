<?php
namespace eMapper\Engine\SQLite;

use eMapper\Engine\Generic\GenericMapper;
use eMapper\Engine\SQLite\Exception\SQLiteMapperException;
use eMapper\Engine\SQLite\Result\SQLiteResultInterface;
use eMapper\Engine\SQLite\Exception\SQLiteQueryException;
use eMapper\Engine\SQLite\Statement\SQLiteStatement;

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
	
	public function __construct($filename, $flags = 0, $encription_key = null) {
		if (empty($flags)) {
			$flags = QLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
		}
		
		try {
			$this->db = empty($encription_key) ? new \SQLite3($filename, $flags) : new \SQLite3($filename, $flags, $encryption_key);
		}
		catch (\Exception $e) {
			throw new SQLiteMapperException($e->getMessage());
		}
	}
		
	public function run_query($query) {
		return $this->db->query($query);
	}
	
	public function free_result($result) {
		if ($result instanceof \SQLite3Result) {
			$result->finalize();
		}
	}
	
	public function close() {
		if ($this->db instanceof \SQLite3) {
			$this->db->close();
		}
	}
	
	protected function build_result_interface($result) {
		return new SQLiteResultInterface($result);
	}
	
	protected function build_statement($query, $args, $parameterMap) {
		$stmt = new SQLiteStatement($this->db, $this->typeManager, $parameterMap);
		return $stmt->build($query, $args, $this->config);
	}
	
	public function __call($method, $args) {
		throw new SQLiteMapperException("This class does not support stored procedures");
	}
	
	/**
	 * TRANSACTION METHODS
	 */
	
	public function begin($txType = self::TX_DEFERRED) {
		switch ($txType) {
			case self::TX_IMMEDIATE:
				$type = 'IMMEDIATE';
				break;
				
			case self::TX_EXCLUSIVE:
				$type = 'EXCLUSIVE';
				break;
			
			case self::TX_DEFERRED:
			default:
				$type = 'DEFERRED';
				break;
		}
		
		return $this->sql("BEGIN $type TRANSACTION");
	}
	
	public function commit() {
		return $this->sql("COMMIT");
	}
	
	public function rollback() {
		return $this->sql("ROLLBACK");
	}
	
	/**
	 * EXCEPTION METHODS
	 */
	
	public function throw_exception($message) {
		throw new SQLiteMapperException($message);
	}
	
	public function throw_query_exception($query) {
		throw new SQLiteQueryException($this->db->lastErrorMsg(), $query);
	}
}
?>