<?php
namespace eMapper\PostgreSQL\Dynamic;

use eMapper\Dynamic\AbstractDynamicSQLStatementTest;
use eMapper\PostgreSQL\PostgreSQLConfig;

/**
 * 
 * @author emaphp
 * @group postgre
 * @group dynamic
 */
class DynamicSQLStatementTest extends AbstractDynamicSQLStatementTest {	
	use PostgreSQLConfig;
	
	public function tearDown() {
		if (is_resource($this->conn))
			pg_close($this->conn);
	}
}
