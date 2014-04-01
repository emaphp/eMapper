<?php
namespace eMapper\PostgreSQL\Callback;

use eMapper\PostgreSQL\PostgreSQLTest;

/**
 * Test setting a custom callback for empty results
 *
 * @author emaphp
 * @group callback
 * @group postgre
 */
class EmptyResultCallbackTest extends PostgreSQLTest {
	/**
	 *
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
			$this->assertInternalType('resource', $result);
			$this->assertEquals(0, pg_num_rows($result));
		})->query("SELECT * FROM users WHERE user_id = 0");
	
		$this->assertNull($value);
	}
}

?>