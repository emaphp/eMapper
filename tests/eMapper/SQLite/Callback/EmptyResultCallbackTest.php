<?php
namespace eMapper\SQLite\Callback;

use eMapper\SQLite\SQLiteTest;

/**
 * Empty result callback tests
 * @author emaphp
 * @group sqlite
 * @group callback
 */
class EmptyResultCallbackTest extends SQLiteTest {
	/**
	 * @throws \UnexpectedValueException
	 * @expectedException UnexpectedValueException
	 */
	public function testNoRowsException () {
		$value = self::$mapper->type('obj')->no_rows(function ($result) {
			throw new \UnexpectedValueException("!!!");
		})->query("SELECT * FROM users WHERE user_id = 0");
	}
	
	public function testNoRowsCallback() {
		$value = self::$mapper->type('obj')->no_rows(function ($result) {
			$this->assertInstanceOf('SQLite3Result', $result);
		})->query("SELECT * FROM users WHERE user_id = 0");
	
		$this->assertNull($value);
	}
}
?>