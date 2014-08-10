<?php
namespace eMapper\PostgreSQL;

use eMapper\Engine\PostgreSQL\Statement\PostgreSQLStatement;
use eMapper\Engine\PostgreSQL\Type\PostgreSQLTypeManager;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Mapper;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;

trait PostgreSQLConfig {
	protected $conn_string = 'host=localhost port=5432 dbname=emapper_testing user=postgres password=c4lpurn14';
	
	protected function getConnection() {
		return pg_connect($this->conn_string);
	}
	
	protected function getResultIterator($result) {
		return new PostgreSQLResultIterator($result);
	}
	
	protected function getMapper() {
		$mapper = new Mapper(new PostgreSQLDriver($this->conn_string));
		$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
		return $mapper;
	}
	
	protected function getStatement() {
		$conn = pg_connect($this->conn_string);
		return new PostgreSQLStatement($conn, new PostgreSQLTypeManager());
	}
}
?>