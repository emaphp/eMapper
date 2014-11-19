<?php
namespace eMapper\Callback;

use eMapper\MapperTest;

abstract class AbstractDebugCallbackTest extends MapperTest {
	public function testDebug() {
		$value = $this->mapper->type('i')
		->debug(function ($query) {
			$this->assertEquals("SELECT 1", $query);
		})->query("SELECT 1");
	
		$this->assertEquals(1, $value);
	}
}
?>