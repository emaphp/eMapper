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
		$this->statement = $this->getStatement();
	}
	
	public function buildMapper() {
		$this->mapper = $this->getMapper();
	}
}
?>