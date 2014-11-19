<?php
namespace eMapper\Callback;

use eMapper\MapperTest;

abstract class AbstractNoRowsCallbackTest extends MapperTest {	
	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testNoRowsException () {
		$value = $this->mapper->type('obj')->emptyCallback(function ($result) {
			throw new \UnexpectedValueException("!!!");
		})->query("SELECT * FROM users WHERE user_id = 0");
	}
}
?>