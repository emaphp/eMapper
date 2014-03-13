<?php
namespace eMapper\SQLite\Callback;

use eMapper\SQLite\SQLiteTest;

/**
 * Tests setting a callback that overrides a query
 * @author emaphp
 * @group sqlite
 * @group callback
 */
class OverrideCallbackTest extends SQLiteTest {
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