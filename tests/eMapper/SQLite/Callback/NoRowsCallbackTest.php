<?php
namespace eMapper\SQLite\Callback;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Callback\AbstractNoRowsCallbackTest;

/**
 * Empty result callback tests
 * @author emaphp
 * @group sqlite
 * @group callback
 */
class NoRowsCallbackTest extends AbstractNoRowsCallbackTest {
	use SQLiteConfig;
	
	public function testNoRowsCallback() {
		$value = $this->mapper->type('obj')->emptyCallback(function ($result) {
			$this->assertInstanceOf('SQLite3Result', $result);
		})->query("SELECT * FROM users WHERE user_id = 0");
	
		$this->assertNull($value);
	}
}
?>