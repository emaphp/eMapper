<?php
namespace eMapper\PostgreSQL\Callback;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Callback\AbstractNoRowsCallbackTest;

/**
 * Test setting a custom callback for empty results
 *
 * @author emaphp
 * @group callback
 * @group postgre
 */
class NoRowsCallbackTest extends AbstractNoRowsCallbackTest {
	use PostgreSQLConfig;
	
	public function testNoRowsCallback() {
		$value = $this->mapper->type('obj')->no_rows(function ($result) {
			$this->assertInternalType('resource', $result);
		})->query("SELECT * FROM users WHERE user_id = 0");
	
		$this->assertNull($value);
	}
}
?>