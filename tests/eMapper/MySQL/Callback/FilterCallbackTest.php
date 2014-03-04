<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLTest;

/**
 * Test setting a filter callback
 * 
 * @author emaphp
 * @group callback
 * @group mysql
 */
class FilterCallbackTest extends MySQLTest {
	public function testSingleInteger() {
		$value = self::$mapper->type('i')->filter(function ($value) {
			return ($value % 2) == 0;
		})->query("SELECT 1");
		
		$this->assertNull($value);
		
		$value = self::$mapper->type('i')->filter(function ($value) {
			return ($value % 2) == 0;
		})->query("SELECT 2");
		
		$this->assertEquals(2, $value);
	}
	
	public function testIntegerList() {
		$values = self::$mapper->type('i[]')->filter(function ($value) {
			return ($value % 2) == 0;
		})->query("SELECT user_id FROM users");
		
		$this->assertFalse(in_array(1, $values));
		$this->assertFalse(in_array(3, $values));
		$this->assertFalse(in_array(5, $values));
		
		$this->assertTrue(in_array(2, $values));
		$this->assertTrue(in_array(4, $values));
	}
	
	public function testSingleObject() {
		$value = self::$mapper->type('obj')->filter(function ($value) {
			return ($value->user_id % 2) == 0;
		})->query("SELECT * FROM users WHERE user_id = 1");
		
		$this->assertNull($value);
		
		$value = self::$mapper->type('obj')->filter(function ($value) {
			return ($value->user_id % 2) == 0;
		})->query("SELECT * FROM users WHERE user_id = 2");
		
		$this->assertInstanceOf('stdClass', $value);
	}
	
	public function testObjectList() {
		$values = self::$mapper->type('obj[]')->filter(function ($value) {
			return ($value->user_id % 2) == 0;
		})->query("SELECT * FROM users ORDER BY user_id ASC");
		
		$this->assertInternalType('array', $values);
		$this->assertCount(2, $values);
		
		//IMPORTANT!!!!
		$this->assertArrayHasKey(1, $values);
		$this->assertArrayHasKey(3, $values);
		
		$this->assertInstanceOf('stdClass', $values[1]);
		$this->assertInstanceOf('stdClass', $values[3]);
		
		$this->assertEquals(2, $values[1]->user_id);
		$this->assertEquals(4, $values[3]->user_id);
	}
	
	public function testIndexedObjectList() {
		$values = self::$mapper->type('obj[user_id]')->filter(function ($value) {
			return ($value->user_id % 2) == 0;
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(2, $values);
	
		//IMPORTANT!!!!
		$this->assertArrayHasKey(2, $values);
		$this->assertArrayHasKey(4, $values);
	
		$this->assertInstanceOf('stdClass', $values[2]);
		$this->assertInstanceOf('stdClass', $values[4]);
	
		$this->assertEquals(2, $values[2]->user_id);
		$this->assertEquals(4, $values[4]->user_id);
	}
	
	public function testGroupedObjectList() {
		$values = self::$mapper->type('obj<category>')->filter(function ($value) {
			return ($value->price >= 150);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
		
		$this->assertInternalType('array', $values);
		$this->assertCount(3, $values);
		
		$this->assertArrayHasKey('Clothes', $values);
		$this->assertArrayHasKey('Hardware', $values);
		$this->assertArrayHasKey('Smartphones', $values);
		
		$this->assertCount(2, $values['Clothes']);
		$this->assertCount(0, $values['Hardware']);
		$this->assertCount(1, $values['Smartphones']);
		
		$this->assertArrayHasKey(0, $values['Clothes']);
		$this->assertArrayHasKey(1, $values['Clothes']);
		$this->assertArrayHasKey(0, $values['Smartphones']);
		
		$this->assertEquals(1, $values['Clothes'][0]->product_id);
		$this->assertEquals(2, $values['Clothes'][1]->product_id);
		$this->assertEquals(5, $values['Smartphones'][0]->product_id);
	}
	
	public function testIndexedGroupedObjectList() {
		$values = self::$mapper->type('obj<category>[product_id]')->filter(function ($value) {
			return ($value->price >= 150);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(3, $values);
	
		$this->assertArrayHasKey('Clothes', $values);
		$this->assertArrayHasKey('Hardware', $values);
		$this->assertArrayHasKey('Smartphones', $values);
	
		$this->assertCount(2, $values['Clothes']);
		$this->assertCount(0, $values['Hardware']);
		$this->assertCount(1, $values['Smartphones']);
	
		$this->assertArrayHasKey(1, $values['Clothes']);
		$this->assertArrayHasKey(2, $values['Clothes']);
		$this->assertArrayHasKey(5, $values['Smartphones']);
	
		$this->assertEquals(1, $values['Clothes'][1]->product_id);
		$this->assertEquals(2, $values['Clothes'][2]->product_id);
		$this->assertEquals(5, $values['Smartphones'][5]->product_id);
	}
	
	public function testSingleArray() {
		$value = self::$mapper->type('arr')->filter(function ($value) {
			return ($value['user_id'] % 2) == 0;
		})->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertNull($value);
	
		$value = self::$mapper->type('arr')->filter(function ($value) {
			return ($value['user_id'] % 2) == 0;
		})->query("SELECT * FROM users WHERE user_id = 2");
	
		$this->assertInternalType('array', $value);
	}
	
	public function testArrayList() {
		$values = self::$mapper->type('array[]')->filter(function ($value) {
			return ($value['user_id'] % 2) == 0;
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(2, $values);
	
		//IMPORTANT!!!!
		$this->assertArrayHasKey(1, $values);
		$this->assertArrayHasKey(3, $values);
	
		$this->assertInternalType('array', $values[1]);
		$this->assertInternalType('array', $values[3]);
	
		$this->assertEquals(2, $values[1]['user_id']);
		$this->assertEquals(4, $values[3]['user_id']);
	}
	
	public function testIndexedArrayList() {
		$values = self::$mapper->type('arr[user_id]')->filter(function ($value) {
			return ($value['user_id'] % 2) == 0;
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(2, $values);
	
		//IMPORTANT!!!!
		$this->assertArrayHasKey(2, $values);
		$this->assertArrayHasKey(4, $values);
	
		$this->assertInternalType('array', $values[2]);
		$this->assertInternalType('array', $values[4]);
	
		$this->assertEquals(2, $values[2]['user_id']);
		$this->assertEquals(4, $values[4]['user_id']);
	}
	
	public function testGroupedArrayList() {
		$values = self::$mapper->type('arr<category>')->filter(function ($value) {
			return ($value['price'] >= 150);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(3, $values);
	
		$this->assertArrayHasKey('Clothes', $values);
		$this->assertArrayHasKey('Hardware', $values);
		$this->assertArrayHasKey('Smartphones', $values);
	
		$this->assertCount(2, $values['Clothes']);
		$this->assertCount(0, $values['Hardware']);
		$this->assertCount(1, $values['Smartphones']);
	
		$this->assertArrayHasKey(0, $values['Clothes']);
		$this->assertArrayHasKey(1, $values['Clothes']);
		$this->assertArrayHasKey(0, $values['Smartphones']);
	
		$this->assertEquals(1, $values['Clothes'][0]['product_id']);
		$this->assertEquals(2, $values['Clothes'][1]['product_id']);
		$this->assertEquals(5, $values['Smartphones'][0]['product_id']);
	}
	
	public function testIndexedGroupedArrayList() {
		$values = self::$mapper->type('arr<category>[product_id]')->filter(function ($value) {
			return ($value['price'] >= 150);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(3, $values);
	
		$this->assertArrayHasKey('Clothes', $values);
		$this->assertArrayHasKey('Hardware', $values);
		$this->assertArrayHasKey('Smartphones', $values);
	
		$this->assertCount(2, $values['Clothes']);
		$this->assertCount(0, $values['Hardware']);
		$this->assertCount(1, $values['Smartphones']);
	
		$this->assertArrayHasKey(1, $values['Clothes']);
		$this->assertArrayHasKey(2, $values['Clothes']);
		$this->assertArrayHasKey(5, $values['Smartphones']);
	
		$this->assertEquals(1, $values['Clothes'][1]['product_id']);
		$this->assertEquals(2, $values['Clothes'][2]['product_id']);
		$this->assertEquals(5, $values['Smartphones'][5]['product_id']);
	}
}	