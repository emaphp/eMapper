<?php
namespace eMapper\PostgreSQL;

use eMapper\Engine\PostgreSQL\Type\PostgreSQLTypeManager;
use eMapper\Engine\PostgreSQL\Statement\PostgreSQLStatement;
use eMapper\AbstractDynamicSQLTest;
use eMapper\Mapper;
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;
use Acme\Type\RGBColorTypeHandler;

/**
 * 
 * @author emaphp
 * @group dynamic
 */
class DynamicSQLTest extends AbstractDynamicSQLTest {
	use PostgreSQLConfig;
	
	public function buildStatement() {
		$conn = pg_connect($this->conn_string);
		$this->statement = new PostgreSQLStatement($conn, new PostgreSQLTypeManager());
	}
	
	public function buildMapper() {
		$this->mapper = new Mapper(new PostgreSQLDriver($this->conn_string));
		$this->mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
	}
}
?>