<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLTest;

/**
 * 
 * @author emaphp
 * @group callback
 */
class OverrideCallbackTest extends MySQLTest {
	public function testOverride() {
		$value = self::$mapper->type('i')->query_override(function ($query) {
			$this->assertEquals("SELECT 1", $query);
			return "SELECT 2";
		})->query("SELECT 1");
		
		$this->assertEquals(2, $value);
	}
	
	public function testNonOverride() {
		$value = self::$mapper->type('i')->query_override(function ($query) {
			$this->assertEquals("SELECT 1", $query);
		})->query("SELECT 1");
		
		$this->assertEquals(1, $value);
	}
}
?>