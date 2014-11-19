<?php
namespace eMapper\MySQL\Dynamic;

use eMapper\Dynamic\AbstractDynamicSQLStatementTest;
use eMapper\MySQL\MySQLConfig;

/**
 * 
 * @author emaphp
 * @group mysql
 * @group dynamic
 */
class DynamicSQLStatementTest extends AbstractDynamicSQLStatementTest {
	use MySQLConfig;
	
	public function tearDown() {
		$this->conn->close();
	}
}