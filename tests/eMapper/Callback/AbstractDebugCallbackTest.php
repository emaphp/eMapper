<?php
namespace eMapper\Callback;

abstract class AbstractDebugCallbackTest extends \PHPUnit_Framework_TestCase {
	protected $mapper;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
	
	public function testDebug() {
		$value = $this->mapper->type('i')
		->debug(function ($query) {
			$this->assertEquals("SELECT 1", $query);
		})->query("SELECT 1");
	
		$this->assertEquals(1, $value);
	}
	
	public function tearDown() {
		$this->mapper->close();
	}
}
?>