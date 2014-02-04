<?php
namespace eMapper\Engine\PostgreSQL;

use eMapper\Engine\Generic\GenericMapper;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultInterface;
use eMapper\Engine\PostgreSQL\Statement\PostgreSQLStatement;
use eMapper\Type\TypeManager;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLMapperException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLQueryException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLConnectionException;

class PostgreSQLMapper extends GenericMapper {
	/**
	 * PostgreSQL connection
	 * @var resource
	 */
	protected $connection;
	
	public function __construct($connection_string, $connect_type = null) {
		if (!is_string($connection_string) || empty($connection_string)) {
			throw new PostgreSQLMapperException("Connection string is not a valid string");
		}
		
		$this->config['db.connection_string'] = $connection_string;
		
		if (!empty($connect_type)) {
			$this->config['db.connect_type'] = $connect_type;
		}
		
		$this->typeManager = new TypeManager();
		
		$this->applyDefaultConfig();
	}
	
	public function connect() {
		if (is_resource($this->connection)) {
			return $this->connection;
		}
		
		$this->connection = empty($this->config['db.connect_type']) ? @pg_connect($this->config['db.connection_string']) : @pg_connect($this->config['db.connection_string'], $this->config['db.connect_type']);
		
		if ($this->connection === false) {
			throw new PostgreSQLConnectionException("Failed to connect to PostgreSQL database. Connection string: " . $this->config['db.connection_string']);
		}
	}
	
	protected function build_statement($query, $args, $parameterMap) {
		$stmt = new PostgreSQLStatement($this->connection, $typeManager, $parameterMap);
		return $stmt->build($query, $args, $this->config);
	}
	
	protected function build_result_interface($result) {
		return new PostgreSQLResultInterface($result);
	}
	
	public function throw_exception($message) {
		throw new PostgreSQLMapperException($message);
	}
	
	public function throw_query_exception($query) {
		throw new PostgreSQLQueryException(pg_last_error($this->connection), $query);
	}
	
	public function beginTransaction() {
		return $this->sql('BEGIN');
	}
	
	public function commit() {
		return $this->sql('COMMIT');
	}
	
	public function rollback() {
		return $this->sql('ROLLBACK');
	}
	
	public function run_query($query) {
		return pg_query($this->connection, $query);
	}
	
	public function close() {
		if (is_resource($this->connection)) {
			pg_close($this->connection);
		}
	}
	
	public function free_result($result) {
		if (is_resource($result)) {
			pg_free_result($result);
		}
	}
}
?>