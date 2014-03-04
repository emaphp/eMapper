<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLTest;

/**
 * Test setting a grouping callback
 * 
 * @author emaphp
 * @group callback
 * @group mysql
 */
class GroupCallbackTest extends MySQLTest {
	public function testClosureGroup() {
		$list = self::$mapper->group(function ($product) {
			return substr($product->manufacture_year, 2);
		})
		->type('obj[]')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $list);
		$this->assertCount(3, $list);
	
		//assert keys
		$this->assertArrayHasKey('11', $list);
		$this->assertArrayHasKey('12', $list);
		$this->assertArrayHasKey('13', $list);
	
		//assert values
		$this->assertCount(2, $list['11']);
		$this->assertInstanceOf('stdClass', $list['11'][0]);
		$this->assertEquals(1, $list['11'][0]->product_id);
		$this->assertInstanceOf('stdClass', $list['11'][1]);
		$this->assertEquals(5, $list['11'][1]->product_id);
	
		$this->assertCount(1, $list['12']);
		$this->assertInstanceOf('stdClass', $list['12'][0]);
		$this->assertEquals(2, $list['12'][0]->product_id);
	
		$this->assertCount(2, $list['13']);
		$this->assertInstanceOf('stdClass', $list['13'][0]);
		$this->assertEquals(3, $list['13'][0]->product_id);
		$this->assertInstanceOf('stdClass', $list['13'][1]);
		$this->assertEquals(4, $list['13'][1]->product_id);
	}
	
	public function createGroup($product) {
		return $product['price'] > 200 ? 'expensive' : 'cheap';
	}
	
	public function testMethodGroup() {
		$list = self::$mapper->group([$this, 'createGroup'])->type('arr[]')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $list);
		$this->assertCount(2, $list);
	
		//assert keys
		$this->assertArrayHasKey('cheap', $list);
		$this->assertArrayHasKey('expensive', $list);
	
		//assert values
		$this->assertCount(3, $list['cheap']);
		$this->assertInternalType('array', $list['cheap'][0]);
		$this->assertEquals(1, $list['cheap'][0]['product_id']);
		$this->assertInternalType('array', $list['cheap'][1]);
		$this->assertEquals(3, $list['cheap'][1]['product_id']);
		$this->assertInternalType('array', $list['cheap'][2]);
		$this->assertEquals(4, $list['cheap'][2]['product_id']);
	
		$this->assertCount(2, $list['expensive']);
		$this->assertInternalType('array', $list['expensive'][0]);
		$this->assertEquals(2, $list['expensive'][0]['product_id']);
		$this->assertInternalType('array', $list['expensive'][1]);
		$this->assertEquals(5, $list['expensive'][1]['product_id']);
	}
}
?>