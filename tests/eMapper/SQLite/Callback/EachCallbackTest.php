<?php
namespace eMapper\SQLite\Callback;

use eMapper\SQLite\SQLiteTest;

/**
 * Each callback tests
 * @author emaphp
 * @group sqlite
 * @group callback
 */
class EachCallbackTest extends SQLiteTest {
	public function testUniqueObject() {
		$user = self::$mapper->type('obj')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
				
			$row->eman = strrev($row->user_name);
		})->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertObjectHasAttribute('eman', $user);
		$this->assertEquals('eodj', $user->eman);
	}
	
	public function testUniqueArray() {
		$user = self::$mapper->type('arr')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
			$row['eman'] = strrev($row['user_name']);
		})->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertArrayHasKey('eman', $user);
		$this->assertEquals('eodj', $user['eman']);
	}
	
	public function testObjectList() {
		$users = self::$mapper->type('obj[]')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
			$row->eman = strrev($row->user_name);
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertCount(5, $users);
	
		foreach ($users as $user) {
			$this->assertObjectHasAttribute('eman', $user);
			$this->assertEquals($user->eman, strrev($user->user_name));
		}
	}
	
	public function testArrayList() {
		$users = self::$mapper->type('array[]')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
			$row['eman'] = strrev($row['user_name']);
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertCount(5, $users);
	
		foreach ($users as $user) {
			$this->assertArrayHasKey('eman', $user);
			$this->assertEquals($user['eman'], strrev($user['user_name']));
		}
	}
	
	public function testIndexedObjectList() {
		$users = self::$mapper->type('obj[user_id:i]')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
			$row->eman = strrev($row->user_name);
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertCount(5, $users);
	
		foreach ($users as $user) {
			$this->assertObjectHasAttribute('eman', $user);
			$this->assertEquals($user->eman, strrev($user->user_name));
		}
	}
	
	public function testIndexedArrayList() {
		$users = self::$mapper->type('array[user_id]')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
			$row['eman'] = strrev($row['user_name']);
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertCount(5, $users);
	
		foreach ($users as $user) {
			$this->assertArrayHasKey('eman', $user);
			$this->assertEquals($user['eman'], strrev($user['user_name']));
		}
	}
	
	public function testGroupedObjectList() {
		$products = self::$mapper->type('obj<category>')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
			$row->edoc = strrev($row->product_code);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		foreach ($products as $group) {
			foreach ($group as $product) {
				$this->assertObjectHasAttribute('edoc', $product);
				$this->assertEquals($product->edoc, strrev($product->product_code));
			}
		}
	}
	
	public function testGroupedArrayList() {
		$products = self::$mapper->type('array<category>')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
			$row['edoc'] = strrev($row['product_code']);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		foreach ($products as $group) {
			foreach ($group as $product) {
				$this->assertArrayHasKey('edoc', $product);
				$this->assertEquals($product['edoc'], strrev($product['product_code']));
			}
		}
	}
	
	public function testGroupedIndexedObjectList() {
		$products = self::$mapper->type('obj<category>[product_id]')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
			$row->edoc = strrev($row->product_code);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		foreach ($products as $group) {
			foreach ($group as $product) {
				$this->assertObjectHasAttribute('edoc', $product);
				$this->assertEquals($product->edoc, strrev($product->product_code));
			}
		}
	}
	
	public function testGroupedIndexedArrayList() {
		$products = self::$mapper->type('array<category>[product_id]')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Engine\SQLite\SQLiteMapper', $mapper);
	
			$row['edoc'] = strrev($row['product_code']);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		foreach ($products as $group) {
			foreach ($group as $product) {
				$this->assertArrayHasKey('edoc', $product);
				$this->assertEquals($product['edoc'], strrev($product['product_code']));
			}
		}
	}
}
?>