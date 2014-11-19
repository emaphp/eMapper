<?php
namespace eMapper\Mapper\ObjectMapper;

use eMapper\MapperTest;

abstract class AbstractResultMapTest extends MapperTest {
	public function testRow() {
		$user = $this->mapper->type('object')
		->resultMap('Acme\Result\UserResultMap')
		->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertInstanceOf('stdClass', $user);
	
		$this->assertObjectHasAttribute('user_id', $user);
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertObjectHasAttribute('name', $user);
		$this->assertInternalType('string', $user->name);
		$this->assertEquals('jdoe', $user->name);
	
		$this->assertObjectHasAttribute('lastLogin', $user);
		$this->assertInternalType('string', $user->lastLogin);
		$this->assertEquals('2013-08-10 19:57:15', $user->lastLogin);
	}
	
	public function testList() {
		$users = $this->mapper->type('object[]')
		->resultMap('Acme\Result\UserResultMap')
		->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$user = $users[0];
		$this->assertInstanceOf('stdClass', $user);
	
		$this->assertObjectHasAttribute('user_id', $user);
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertObjectHasAttribute('name', $user);
		$this->assertInternalType('string', $user->name);
		$this->assertEquals('jdoe', $user->name);
	
		$this->assertObjectHasAttribute('lastLogin', $user);
		$this->assertInternalType('string', $user->lastLogin);
		$this->assertEquals('2013-08-10 19:57:15', $user->lastLogin);
	}
	
	public function testIndexedList() {
		$users = $this->mapper->type('object[user_id]')
		->resultMap('Acme\Result\UserResultMap')
		->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$user = $users[1];
		$this->assertInstanceOf('stdClass', $user);
	
		$this->assertObjectHasAttribute('user_id', $user);
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertObjectHasAttribute('name', $user);
		$this->assertInternalType('string', $user->name);
		$this->assertEquals('jdoe', $user->name);
	
		$this->assertObjectHasAttribute('lastLogin', $user);
		$this->assertInternalType('string', $user->lastLogin);
		$this->assertEquals('2013-08-10 19:57:15', $user->lastLogin);
	}
	
	public function testCustomIndexList() {
		$users = $this->mapper->type('object[user_id:s]')
		->resultMap('Acme\Result\UserResultMap')
		->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
	
		$user = $users['1'];
		$this->assertInstanceOf('stdClass', $user);
	
		$this->assertObjectHasAttribute('user_id', $user);
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertObjectHasAttribute('name', $user);
		$this->assertInternalType('string', $user->name);
		$this->assertEquals('jdoe', $user->name);
	
		$this->assertObjectHasAttribute('lastLogin', $user);
		$this->assertInternalType('string', $user->lastLogin);
		$this->assertEquals('2013-08-10 19:57:15', $user->lastLogin);
	}
	
	public function testOverrideIndexList() {
		$products = $this->mapper->type('object[category]')
		->resultMap('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		////
		$product = $products['Clothes'];
		$this->assertInstanceOf('stdClass', $product);
		$this->assertEquals('IND00232', $product->code);
	
		////
		$product = $products['Hardware'];
		$this->assertInstanceOf('stdClass', $product);
		$this->assertEquals('GFX00067', $product->code);
	
		////
		$product = $products['Smartphones'];
		$this->assertInstanceOf('stdClass', $product);
		$this->assertEquals('PHN00666', $product->code);
		
		////
		$product = $products['Laptops'];
		$this->assertInstanceOf('stdClass', $product);
		$this->assertEquals('TEC00103', $product->code);
		
		////
		$product = $products['Software'];
		$this->assertInstanceOf('stdClass', $product);
		$this->assertEquals('SOFT0134', $product->code);
	}
	
	public function testGroupedList() {
		$products = $this->mapper->type('object<category>')
		->resultMap('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(2, $products['Smartphones']);
		$this->assertInternalType('array', $products['Laptops']);
		$this->assertCount(1, $products['Laptops']);
		$this->assertInternalType('array', $products['Software']);
		$this->assertCount(1, $products['Software']);
	
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertInstanceOf('\stdClass', $products['Clothes'][0]);
		$this->assertEquals('IND00054', $products['Clothes'][0]->code);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertInstanceOf('\stdClass', $products['Clothes'][1]);
		$this->assertEquals('IND00043', $products['Clothes'][1]->code);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertInstanceOf('\stdClass', $products['Clothes'][2]);
		$this->assertEquals('IND00232', $products['Clothes'][2]->code);
		$this->assertArrayHasKey(0, $products['Hardware']);
		$this->assertInstanceOf('\stdClass', $products['Hardware'][0]);
		$this->assertEquals('GFX00067', $products['Hardware'][0]->code);
		$this->assertArrayHasKey(0, $products['Smartphones']);
		$this->assertInstanceOf('\stdClass', $products['Smartphones'][0]);
		$this->assertEquals('PHN00098', $products['Smartphones'][0]->code);
		$this->assertArrayHasKey(1, $products['Smartphones']);
		$this->assertInstanceOf('\stdClass', $products['Smartphones'][1]);
		$this->assertEquals('PHN00666', $products['Smartphones'][1]->code);
		$this->assertArrayHasKey(0, $products['Laptops']);
		$this->assertInstanceOf('\stdClass', $products['Laptops'][0]);
		$this->assertEquals('TEC00103', $products['Laptops'][0]->code);
		$this->assertArrayHasKey(0, $products['Software']);
		$this->assertInstanceOf('\stdClass', $products['Software'][0]);
		$this->assertEquals('SOFT0134', $products['Software'][0]->code);
	
		$product = $products['Clothes'][0];
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('float', $product->price);
		$this->assertEquals(150.65, $product->price);
	
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	}
	
	public function testGroupedIndexedList() {
		$products = $this->mapper->type('object<category>[code]')
		->resultMap('Acme\Result\GenericProductResultMap')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(2, $products['Smartphones']);
		$this->assertInternalType('array', $products['Laptops']);
		$this->assertCount(1, $products['Laptops']);
		$this->assertInternalType('array', $products['Software']);
		$this->assertCount(1, $products['Software']);
	
		$this->assertArrayHasKey('IND00054', $products['Clothes']);
		$this->assertInstanceOf('\stdClass', $products['Clothes']['IND00054']);
		$this->assertEquals('IND00054', $products['Clothes']['IND00054']->code);
		$this->assertArrayHasKey('IND00043', $products['Clothes']);
		$this->assertInstanceOf('\stdClass', $products['Clothes']['IND00043']);
		$this->assertEquals('IND00043', $products['Clothes']['IND00043']->code);
		$this->assertArrayHasKey('IND00232', $products['Clothes']);
		$this->assertInstanceOf('\stdClass', $products['Clothes']['IND00232']);
		$this->assertEquals('IND00232', $products['Clothes']['IND00232']->code);
		$this->assertArrayHasKey('GFX00067', $products['Hardware']);
		$this->assertInstanceOf('\stdClass', $products['Hardware']['GFX00067']);
		$this->assertEquals('GFX00067', $products['Hardware']['GFX00067']->code);
		$this->assertArrayHasKey('PHN00098', $products['Smartphones']);
		$this->assertInstanceOf('\stdClass', $products['Smartphones']['PHN00098']);
		$this->assertEquals('PHN00098', $products['Smartphones']['PHN00098']->code);
		$this->assertArrayHasKey('PHN00666', $products['Smartphones']);
		$this->assertInstanceOf('\stdClass', $products['Smartphones']['PHN00666']);
		$this->assertEquals('PHN00666', $products['Smartphones']['PHN00666']->code);
		$this->assertArrayHasKey('TEC00103', $products['Laptops']);
		$this->assertInstanceOf('\stdClass', $products['Laptops']['TEC00103']);
		$this->assertEquals('TEC00103', $products['Laptops']['TEC00103']->code);
		$this->assertArrayHasKey('SOFT0134', $products['Software']);
		$this->assertInstanceOf('\stdClass', $products['Software']['SOFT0134']);
		$this->assertEquals('SOFT0134', $products['Software']['SOFT0134']->code);
	
		$product = $products['Clothes']['IND00054'];
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
	
		$this->assertInternalType('string', $product->code);
		$this->assertEquals('IND00054', $product->code);
	
		$this->assertInternalType('float', $product->price);
		$this->assertEquals(150.65, $product->price);
	
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
	
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
	}
}
?>