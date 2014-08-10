<?php
namespace eMapper\MySQL;

use eMapper\AbstractDynamicSQLTest;

/**
 * Test dynamic sql expressions
 * 
 * @author emaphp
 * @group dynamic
 * @group mysql
 */
class DynamicSQLTest extends AbstractDynamicSQLTest {
	use MySQLConfig;
	
	public function buildStatement() {
		$this->statement = $this->getStatement();
	}
	
	public function buildMapper() {
		$this->mapper = $this->getMapper();
	}
}
?>