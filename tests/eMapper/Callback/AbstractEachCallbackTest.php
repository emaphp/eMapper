<?php
namespace eMapper\Callback;

use eMapper\MapperTest;

abstract class AbstractEachCallbackTest extends MapperTest {
	public function testUniqueObject() {
		$user = $this->mapper->type('obj')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
				
			$row->eman = strrev($row->user_name);
		})->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertObjectHasAttribute('eman', $user);
		$this->assertEquals('eodj', $user->eman);
	}
	
	public function testUniqueArray() {
		$user = $this->mapper->type('arr')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
	
			$row['eman'] = strrev($row['user_name']);
		})->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertArrayHasKey('eman', $user);
		$this->assertEquals('eodj', $user['eman']);
	}
	
	public function testObjectList() {
		$users = $this->mapper->type('obj[]')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
	
			$row->eman = strrev($row->user_name);
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertCount(5, $users);
	
		foreach ($users as $user) {
			$this->assertObjectHasAttribute('eman', $user);
			$this->assertEquals($user->eman, strrev($user->user_name));
		}
	}
	
	public function testArrayList() {
		$users = $this->mapper->type('array[]')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
	
			$row['eman'] = strrev($row['user_name']);
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertCount(5, $users);
	
		foreach ($users as $user) {
			$this->assertArrayHasKey('eman', $user);
			$this->assertEquals($user['eman'], strrev($user['user_name']));
		}
	}
	
	public function testIndexedObjectList() {
		$users = $this->mapper->type('obj[user_id]')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
	
			$row->eman = strrev($row->user_name);
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertCount(5, $users);
	
		foreach ($users as $user) {
			$this->assertObjectHasAttribute('eman', $user);
			$this->assertEquals($user->eman, strrev($user->user_name));
		}
	}
	
	public function testIndexedArrayList() {
		$users = $this->mapper->type('array[user_id]')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
	
			$row['eman'] = strrev($row['user_name']);
		})->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertCount(5, $users);
	
		foreach ($users as $user) {
			$this->assertArrayHasKey('eman', $user);
			$this->assertEquals($user['eman'], strrev($user['user_name']));
		}
	}
	
	public function testGroupedObjectList() {
		$products = $this->mapper->type('obj<category>')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
	
			$row->edoc = strrev($row->product_code);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertCount(5, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		foreach ($products as $group) {
			foreach ($group as $product) {
				$this->assertObjectHasAttribute('edoc', $product);
				$this->assertEquals($product->edoc, strrev($product->product_code));
			}
		}
	}
	
	public function testGroupedArrayList() {
		$products = $this->mapper->type('array<category>')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
	
			$row['edoc'] = strrev($row['product_code']);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertCount(5, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		foreach ($products as $group) {
			foreach ($group as $product) {
				$this->assertArrayHasKey('edoc', $product);
				$this->assertEquals($product['edoc'], strrev($product['product_code']));
			}
		}
	}
	
	public function testGroupedIndexedObjectList() {
		$products = $this->mapper->type('obj<category>[product_id]')->each(function($row, $mapper) {
			$this->assertInstanceOf('stdClass', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
	
			$row->edoc = strrev($row->product_code);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertCount(5, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		foreach ($products as $group) {
			foreach ($group as $product) {
				$this->assertObjectHasAttribute('edoc', $product);
				$this->assertEquals($product->edoc, strrev($product->product_code));
			}
		}
	}
	
	public function testGroupedIndexedArrayList() {
		$products = $this->mapper->type('array<category>[product_id]')->each(function(&$row, $mapper) {
			$this->assertInternalType('array', $row);
			$this->assertInstanceOf('eMapper\Mapper', $mapper);
	
			$row['edoc'] = strrev($row['product_code']);
		})->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertCount(5, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		foreach ($products as $group) {
			foreach ($group as $product) {
				$this->assertArrayHasKey('edoc', $product);
				$this->assertEquals($product['edoc'], strrev($product['product_code']));
			}
		}
	}
}
?>