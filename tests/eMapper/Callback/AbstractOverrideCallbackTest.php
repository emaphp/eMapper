<?php
namespace eMapper\Callback;

class AbstractOverrideCallbackTest extends \PHPUnit_Framework_TestCase {
	protected $mapper;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
	
	public function testOverride() {
		$value = $this->mapper->type('i')->query_override(function ($query) {
			$this->assertEquals("SELECT 1", $query);
			return "SELECT 2";
		})->query("SELECT 1");
	
		$this->assertEquals(2, $value);
	}
	
	public function testNonOverride() {
		$value = $this->mapper->type('i')->query_override(function ($query) {
			$this->assertEquals("SELECT 1", $query);
		})->query("SELECT 1");
	
		$this->assertEquals(1, $value);
	}
}
?>