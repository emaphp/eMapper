<?php
namespace eMapper\Callback;

abstract class AbstractNoRowsCallbackTest extends \PHPUnit_Framework_TestCase {
	protected $mapper;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
	
	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testNoRowsException () {
		$value = $this->mapper->type('obj')->no_rows(function ($result) {
			throw new \UnexpectedValueException("!!!");
		})->query("SELECT * FROM users WHERE user_id = 0");
	}
}
?>