<?php
namespace eMapper\SQLite\Callback;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Callback\AbstractIndexCallbackTest;

/**
 * Index callback tests
 * @author emaphp
 * @group sqlite
 * @group callback
 */
class IndexCallbackTest extends AbstractIndexCallbackTest {
	use SQLiteConfig;
	
	public function testClosureIndex() {
		$list = $this->mapper->indexCallback(function ($user) {
			$date = \DateTime::createFromFormat('Y-m-d', $user->birth_date);
			return intval($date->format('Y'));
		})
		->type('obj[]')->query("SELECT * FROM users");
	
		$this->assertInternalType('array', $list);
		$this->assertCount(5, $list);
	
		//assert indexes
		$this->assertArrayHasKey(1987, $list);
		$this->assertArrayHasKey(1976, $list);
		$this->assertArrayHasKey(1967, $list);
		$this->assertArrayHasKey(1980, $list);
		$this->assertArrayHasKey(1977, $list);
	
		//assert values
		$this->assertInstanceOf('stdClass', $list[1987]);
		$this->assertInstanceOf('stdClass', $list[1976]);
		$this->assertInstanceOf('stdClass', $list[1967]);
		$this->assertInstanceOf('stdClass', $list[1980]);
		$this->assertInstanceOf('stdClass', $list[1977]);
	
		$this->assertEquals(1, $list[1987]->user_id);
		$this->assertEquals(2, $list[1976]->user_id);
		$this->assertEquals(3, $list[1967]->user_id);
		$this->assertEquals(4, $list[1980]->user_id);
		$this->assertEquals(5, $list[1977]->user_id);
	}
}
?>