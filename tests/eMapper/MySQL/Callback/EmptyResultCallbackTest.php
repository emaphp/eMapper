<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLTest;

/**
 * 
 * @author emaphp
 * @group callback
 */
class EmptyResultCallbackTest extends MySQLTest {
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
			$this->assertInstanceOf('mysqli_result', $result);
			$this->assertEquals(0, $result->num_rows);
		})->query("SELECT * FROM users WHERE user_id = 0");

		$this->assertNull($value);
	}
}
?>