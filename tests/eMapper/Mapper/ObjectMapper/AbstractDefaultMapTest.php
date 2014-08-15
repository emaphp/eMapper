<?php
namespace eMapper\Mapper\ObjectMapper;

use eMapper\Mapper\AbstractMapperTest;

abstract class AbstractDefaultMapTest extends AbstractMapperTest {
	public function testRow() {
		$user = $this->mapper->type('object')->query("SELECT * FROM users WHERE user_id = 1");
	
		$this->assertInstanceOf('stdClass', $user);
	
		$this->assertObjectHasAttribute('user_id', $user);
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertObjectHasAttribute('user_name', $user);
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $user);
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertObjectHasAttribute('last_login', $user);
		$this->assertInstanceOf('DateTime', $user->last_login);
		$this->assertEquals('2013-08-10 19:57:15', $user->last_login->format('Y-m-d H:i:s'));
	
		$this->assertObjectHasAttribute('newsletter_time', $user);
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $user);
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testList() {
		$users = $this->mapper->type('object[]')->query("SELECT * FROM users ORDER BY user_id ASC");
	
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
	
		$this->assertObjectHasAttribute('user_name', $user);
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $user);
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertObjectHasAttribute('last_login', $user);
		$this->assertInstanceOf('DateTime', $user->last_login);
		$this->assertEquals('2013-08-10 19:57:15', $user->last_login->format('Y-m-d H:i:s'));
	
		$this->assertObjectHasAttribute('newsletter_time', $user);
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $user);
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testIndexedList() {
		$users = $this->mapper->type('object[user_id]')->query("SELECT * FROM users ORDER BY user_id ASC");
	
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
	
		$this->assertObjectHasAttribute('user_name', $user);
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $user);
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertObjectHasAttribute('last_login', $user);
		$this->assertInstanceOf('DateTime', $user->last_login);
		$this->assertEquals('2013-08-10 19:57:15', $user->last_login->format('Y-m-d H:i:s'));
	
		$this->assertObjectHasAttribute('newsletter_time', $user);
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $user);
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testCustomIndexList() {
		$users = $this->mapper->type('object[user_id:string]')->query("SELECT * FROM users ORDER BY user_id ASC");
	
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
	
		$this->assertObjectHasAttribute('user_name', $user);
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $user);
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertObjectHasAttribute('last_login', $user);
		$this->assertInstanceOf('DateTime', $user->last_login);
		$this->assertEquals('2013-08-10 19:57:15', $user->last_login->format('Y-m-d H:i:s'));
	
		$this->assertObjectHasAttribute('newsletter_time', $user);
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $user);
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals($this->getBlob(), $user->avatar);
	}
	
	public function testOverrideIndexList() {
		$products = $this->mapper->type('object[category]')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		////
		$product = $products['Clothes'];
		$this->assertInstanceOf('stdClass', $product);
	
		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(3, $product->product_id);
	
		$this->assertObjectHasAttribute('product_code', $product);
		$this->assertInternalType('string', $product->product_code);
		$this->assertEquals('IND00232', $product->product_code);
	
		$this->assertObjectHasAttribute('description', $product);
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Green shirt', $product->description);
	
		$this->assertObjectHasAttribute('color', $product);
		$this->assertInternalType('string', $product->color);
		$this->assertEquals('707c04', $product->color);
	
		$this->assertObjectHasAttribute('price', $product);
		$this->assertInternalType('float', $product->price);
		$this->assertEquals(70.9, $product->price);
	
		$this->assertObjectHasAttribute('category', $product);
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
	
		$this->assertObjectHasAttribute('rating', $product);
		$this->assertInternalType('float', $product->rating);
		$this->assertEquals(4.1, $product->rating);
	
		$this->assertObjectHasAttribute('refurbished', $product);
		$this->assertInternalType('integer', $product->refurbished);
		$this->assertEquals(0, $product->refurbished);
	
		$this->assertObjectHasAttribute('manufacture_year', $product);
		$this->assertInternalType('integer', $product->manufacture_year);
		$this->assertEquals(2013, $product->manufacture_year);
	
		////
		$product = $products['Hardware'];
		$this->assertInstanceOf('stdClass', $product);
		$this->assertEquals(4, $product->product_id);
	
		////
		$product = $products['Smartphones'];
		$this->assertInstanceOf('stdClass', $product);
		$this->assertEquals(5, $product->product_id);
	}
	
	public function testGroupedList() {
		$products = $this->mapper->type('object<category>')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
	
		//
		$product = $products['Clothes'][0];
		$this->assertInstanceOf('stdClass', $product);
	
		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
	
		$this->assertObjectHasAttribute('product_code', $product);
		$this->assertInternalType('string', $product->product_code);
		$this->assertEquals('IND00054', $product->product_code);
	
		$this->assertObjectHasAttribute('description', $product);
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);

		$this->assertObjectHasAttribute('color', $product);
		$this->assertInternalType('string', $product->color);
		$this->assertEquals('e11a1a', $product->color);

		$this->assertObjectHasAttribute('price', $product);
		$this->assertInternalType('float', $product->price);
		$this->assertEquals(150.65, $product->price);

		$this->assertObjectHasAttribute('category', $product);
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);

		$this->assertObjectHasAttribute('rating', $product);
		$this->assertInternalType('float', $product->rating);
		$this->assertEquals(4.5, $product->rating);

		$this->assertObjectHasAttribute('refurbished', $product);
		$this->assertInternalType('integer', $product->refurbished);
		$this->assertEquals(0, $product->refurbished);

		$this->assertObjectHasAttribute('manufacture_year', $product);
		$this->assertInternalType('integer', $product->manufacture_year);
		$this->assertEquals(2011, $product->manufacture_year);

		////
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertInternalType('array', $products);
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(0, $products['Hardware']);

		$product = $products['Hardware'][0];
		$this->assertInstanceOf('stdClass', $product);

		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(4, $product->product_id);

		////
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertInternalType('array', $products);
		$this->assertCount(1, $products['Smartphones']);
		$this->assertArrayHasKey(0, $products['Smartphones']);

		$product = $products['Smartphones'][0];
		$this->assertInstanceOf('stdClass', $product);

		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(5, $product->product_id);
	}
	
	public function testGroupedIndexedList() {
		$products = $this->mapper->type('object<category>[product_id]')->query("SELECT * FROM products ORDER BY product_id ASC");
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
	
		//
		$product = $products['Clothes'][1];
		$this->assertInstanceOf('stdClass', $product);
	
		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
	
		$this->assertObjectHasAttribute('product_code', $product);
		$this->assertInternalType('string', $product->product_code);
		$this->assertEquals('IND00054', $product->product_code);
	
		$this->assertObjectHasAttribute('description', $product);
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);

		$this->assertObjectHasAttribute('color', $product);
		$this->assertInternalType('string', $product->color);
		$this->assertEquals('e11a1a', $product->color);

		$this->assertObjectHasAttribute('price', $product);
		$this->assertInternalType('float', $product->price);
		$this->assertEquals(150.65, $product->price);

		$this->assertObjectHasAttribute('category', $product);
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);

		$this->assertObjectHasAttribute('rating', $product);
		$this->assertInternalType('float', $product->rating);
		$this->assertEquals(4.5, $product->rating);

		$this->assertObjectHasAttribute('refurbished', $product);
		$this->assertInternalType('integer', $product->refurbished);
		$this->assertEquals(0, $product->refurbished);

		$this->assertObjectHasAttribute('manufacture_year', $product);
		$this->assertInternalType('integer', $product->manufacture_year);
		$this->assertEquals(2011, $product->manufacture_year);

		////
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertInternalType('array', $products);
		$this->assertCount(1, $products['Hardware']);
		$this->assertArrayHasKey(4, $products['Hardware']);

		$product = $products['Hardware'][4];
		$this->assertInstanceOf('stdClass', $product);

		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(4, $product->product_id);

		////
		$this->assertArrayHasKey('Smartphones', $products);
		$this->assertInternalType('array', $products);
		$this->assertCount(1, $products['Smartphones']);
		$this->assertArrayHasKey(5, $products['Smartphones']);

		$product = $products['Smartphones'][5];
		$this->assertInstanceOf('stdClass', $product);

		$this->assertObjectHasAttribute('product_id', $product);
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(5, $product->product_id);
	}
}
?>