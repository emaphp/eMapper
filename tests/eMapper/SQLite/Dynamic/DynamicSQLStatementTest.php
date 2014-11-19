<?php
namespace eMapper\SQLite\Dynamic;

use eMapper\Dynamic\AbstractDynamicSQLStatementTest;
use eMapper\SQLite\SQLiteConfig;

/**
 * 
 * @author emaphp
 * @group sqlite
 * @group dynamic
 */
class DynamicSQLStatementTest extends AbstractDynamicSQLStatementTest {	
	use SQLiteConfig;
	
	public function tearDown() {
		$this->conn->close();
	}
}