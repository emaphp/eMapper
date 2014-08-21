<?php
namespace eMapper\Mapper\ObjectMapper;

use eMapper\Mapper\AbstractMapperTest;

abstract class AbstractObjectMapperTest extends AbstractMapperTest {
	public function testRow() {
		$user = $this->mapper->type('obj:Acme\Generic\GenericUser')
		->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testList() {
		$users = $this->mapper->type('obj:Acme\Generic\GenericUser[]')
		->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$user = $users[0];
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testIndexedList() {
		$users = $this->mapper->type('obj:Acme\Generic\GenericUser[user_id]')
		->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$user = $users[1];
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testOverrideIndexList() {
		$products = $this->mapper->type('obj:Acme\Generic\Product[category]')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Software', $products);
	
		////
		$product = $products['Clothes'];
		$this->assertInstanceOf('Acme\Generic\Product', $product);
		$this->assertEquals(3, $product->product_id);
	
		////
		$product = $products['Hardware'];
		$this->assertInstanceOf('Acme\Generic\Product', $product);
		$this->assertEquals(4, $product->product_id);
	
		////
		$product = $products['Smartphones'];
		$this->assertInstanceOf('Acme\Generic\Product', $product);
		$this->assertEquals(7, $product->product_id);
		
		////
		$product = $products['Laptops'];
		$this->assertInstanceOf('Acme\Generic\Product', $product);
		$this->assertEquals(6, $product->product_id);
		
		////
		$product = $products['Software'];
		$this->assertInstanceOf('Acme\Generic\Product', $product);
		$this->assertEquals(8, $product->product_id);
	}
	
	public function testCustomIndexList() {
		$users = $this->mapper->type('obj:Acme\Generic\GenericUser[user_id:string]')
		->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
	
		$user = $users['1'];
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testGroupedList() {
		$products = $this->mapper->type('obj:Acme\Generic\Product<category>')
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
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][0]);
		$this->assertEquals(1, $products['Clothes'][0]->product_id);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][1]);
		$this->assertEquals(2, $products['Clothes'][1]->product_id);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][2]);
		$this->assertEquals(3, $products['Clothes'][2]->product_id);
		$this->assertArrayHasKey(0, $products['Hardware']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Hardware'][0]);
		$this->assertEquals(4, $products['Hardware'][0]->product_id);
		$this->assertArrayHasKey(0, $products['Smartphones']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Smartphones'][0]);
		$this->assertEquals(5, $products['Smartphones'][0]->product_id);
		$this->assertArrayHasKey(1, $products['Smartphones']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Smartphones'][1]);
		$this->assertEquals(7, $products['Smartphones'][1]->product_id);
		$this->assertArrayHasKey(0, $products['Laptops']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Laptops'][0]);
		$this->assertEquals(6, $products['Laptops'][0]->product_id);
		$this->assertArrayHasKey(0, $products['Software']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Software'][0]);
		$this->assertEquals(8, $products['Software'][0]->product_id);
	
		$product = $products['Clothes'][0];
	
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
	
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
	
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
	}
	
	public function testGroupedIndexedList() {
		$products = $this->mapper->type('obj:Acme\Generic\Product<category>[product_id]')
		->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertArrayHasKey('Laptops', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
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
	
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][1]);
		$this->assertEquals(1, $products['Clothes'][1]->product_id);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][2]);
		$this->assertEquals(2, $products['Clothes'][2]->product_id);
		$this->assertArrayHasKey(3, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][3]);
		$this->assertEquals(3, $products['Clothes'][3]->product_id);
		$this->assertArrayHasKey(4, $products['Hardware']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Hardware'][4]);
		$this->assertEquals(4, $products['Hardware'][4]->product_id);
		$this->assertArrayHasKey(5, $products['Smartphones']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Smartphones'][5]);
		$this->assertEquals(5, $products['Smartphones'][5]->product_id);
		$this->assertArrayHasKey(7, $products['Smartphones']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Smartphones'][7]);
		$this->assertEquals(7, $products['Smartphones'][7]->product_id);
		$this->assertArrayHasKey(6, $products['Laptops']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Laptops'][6]);
		$this->assertEquals(6, $products['Laptops'][6]->product_id);
		$this->assertArrayHasKey(8, $products['Software']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Software'][8]);
		$this->assertEquals(8, $products['Software'][8]->product_id);
	
		$product = $products['Clothes'][1];
	
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
	
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
	
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
	}
}
?>