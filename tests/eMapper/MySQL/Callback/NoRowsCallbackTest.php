<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLConfig;
use eMapper\Callback\AbstractNoRowsCallbackTest;

/**
 * Test setting a custom callback for empty results
 * 
 * @author emaphp
 * @group callback
 * @group mysql
 */
class NoRowsCallbackTest extends AbstractNoRowsCallbackTest {
	use MySQLConfig;
	
	public function testNoRowsCallback() {
		$value = $this->mapper->type('obj')->no_rows(function ($result) {
			$this->assertInstanceOf('mysqli_result', $result);
			$this->assertEquals(0, $result->num_rows);
		})->query("SELECT * FROM users WHERE user_id = 0");
	
		$this->assertNull($value);
	}
}
?>