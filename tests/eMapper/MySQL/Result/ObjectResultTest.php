<?php
namespace eMapper\MySQL\Result;

use eMapper\MySQL\MySQLTest;
use eMapper\Result\Mapper\ObjectTypeMapper;
use eMapper\Type\TypeManager;
use eMapper\Engine\MySQL\Result\MySQLResultInterface;
use Acme\Type\RGBColorTypeHandler;

/**
 * Test ObjectTypeMapper class with various results
 * 
 * @author emaphp
 * @group mysql
 * @group result
 */
class ObjectResultTest extends MySQLTest {
	/*
	 * WITHOUT RESULT MAP
	 */
	
	/**
	 * Using a custom mapping class
	 */
	public function testRow() {
		$mapper = new ObjectTypeMapper(new TypeManager(), null, 'Acme\Generic\GenericUser');
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 1");
		$user = $mapper->mapResult(new MySQLResultInterface($result));
		
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
		$this->assertEquals(self::$blob, $user->avatar);
		
		$result->free();
	}
	
	/**
	 * Mapping to list using a custom mapping class
	 */
	public function testList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), null, 'Acme\Generic\GenericUser');
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new MySQLResultInterface($result));
		
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
		$this->assertEquals(self::$blob, $user->avatar);
		
		$result->free();
	}
	
	/**
	 * Mapping to a indexed list using a custom mapping class
	 */
	public function testIndexedList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), null, 'Acme\Generic\GenericUser');
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new MySQLResultInterface($result), 'user_id');

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
		$this->assertEquals(self::$blob, $user->avatar);
	
		$result->free();
	}
	
	/**
	 * Mapping to a indexed list with a custom index type using a custom mapping class
	 */
	public function testStringIndexedList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), null, 'Acme\Generic\GenericUser');
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new MySQLResultInterface($result), 'user_id', 'string');
	
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
		$this->assertEquals(self::$blob, $user->avatar);
	
		$result->free();
	}
	
	public function testGroupedList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), null, 'Acme\Generic\Product');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result), null, null, 'category');
		
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
		
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
		
		$product = $products['Clothes'][0];
		
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
		
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
		
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
		
		$result->free();
	}
	
	public function testCustomGroupedList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), null, 'Acme\Generic\Product');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result), null, null, 'category', 'string');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
	
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
	
		$product = $products['Clothes'][0];
		
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
		
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
		
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
		
		$result->free();
	}
	
	public function testGroupedIndexedList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), null, 'Acme\Generic\Product');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result), 'product_id', null, 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
	
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
	
		$product = $products['Clothes'][1];
		
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
		
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
		
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
		
		$result->free();
	}
	
	public function testCustomGroupedIndexedList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), null, 'Acme\Generic\Product');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result), 'product_id', 'string', 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
	
		$this->assertArrayHasKey('1', $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes']['1']);
		$this->assertEquals(1, $products['Clothes']['1']->product_id);
		$this->assertArrayHasKey('2', $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes']['2']);
		$this->assertEquals(2, $products['Clothes']['2']->product_id);
		$this->assertArrayHasKey('3', $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes']['3']);
		$this->assertEquals(3, $products['Clothes']['3']->product_id);
		$this->assertArrayHasKey('4', $products['Hardware']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Hardware']['4']);
		$this->assertEquals(4, $products['Hardware']['4']->product_id);
		$this->assertArrayHasKey('5', $products['Smartphones']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Smartphones']['5']);
		$this->assertEquals(5, $products['Smartphones']['5']->product_id);
	
		$product = $products['Clothes']['1'];
		
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
		
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
		
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
		
		$result->free();
	}
	
	/*
	 * WITH RESULT MAP
	 */
	
	/**
	 * Using a result map
	 */
	public function testResultMap() {
		$mapper = new ObjectTypeMapper(new TypeManager(), 'Acme\Result\UserResultMap');
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 1");
		$user = $mapper->mapResult(new MySQLResultInterface($result));
		
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
		
		$result->free();
	}
	
	/**
	 * Mapping to a list using a result map
	 */
	public function testResultMapList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), 'Acme\Result\UserResultMap');
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new MySQLResultInterface($result));
		
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
		
		$result->free();
	}
	
	/**
	 * Mapping to an indexed list using a result map
	 */
	public function testResultMapIndexedList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), 'Acme\Result\UserResultMap');
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new MySQLResultInterface($result), 'user_id');
	
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
	
		$result->free();
	}
	
	/**
	 * Mapping to an indexed list with a custom index map using a result map
	 */
	public function testResultMapStringIndexedList() {
		$mapper = new ObjectTypeMapper(new TypeManager(), 'Acme\Result\UserResultMap');
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new MySQLResultInterface($result), 'user_id', 's');
	
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
	
		$result->free();
	}
	
	public function testResultMapGroupedList() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
		
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result), null, null, 'category');
		
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
		
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
		
		$result->free();
	}
	
	public function testCustomResultMapGroupedList() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
		
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result), null, null, 'category', 'string');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
	
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
		
		$result->free();
	}
	
	public function testResultMapGroupedIndexedList() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
		
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result), 'code', null, 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
	
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
		
		$result->free();
	}
	
	/**
	 * Mapping to a custom class using a result map
	 */
	public function testResultClassMap() {
		//setup type manager
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
		
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 1");
		$product = $mapper->mapResult(new MySQLResultInterface($result));
	
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
	
		$this->assertInternalType('string', $product->getDescription());
		$this->assertEquals('Red dress', $product->getDescription());
	
		$this->assertInternalType('string', $product->getCode());
		$this->assertEquals('IND00054', $product->getCode());
	
		$this->assertInternalType('float', $product->getPrice());
		$this->assertEquals(150.65, $product->getPrice());
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
		
		$this->assertInstanceOf('Acme\RGBColor', $product->getColor());
	
		$result->free();
	}
	
	/**
	 * Mapping to a list of a custom class using a result map
	 */
	public function testResultClassMapList() {
		//setup type manager
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result));
		
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
		$this->assertArrayHasKey(0, $products);
		$this->assertArrayHasKey(1, $products);
		$this->assertArrayHasKey(2, $products);
		$this->assertArrayHasKey(3, $products);
		$this->assertArrayHasKey(4, $products);
	
		$product = $products[0];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
	
		$this->assertInternalType('string', $product->getDescription());
		$this->assertEquals('Red dress', $product->getDescription());
	
		$this->assertInternalType('string', $product->getCode());
		$this->assertEquals('IND00054', $product->getCode());
	
		$this->assertInternalType('float', $product->getPrice());
		$this->assertEquals(150.65, $product->getPrice());
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->getColor());
	
		$result->free();
	}
	
	/**
	 * Mapping to an indexed list of a custom class using a result map
	 */
	public function testResultClassMapIndexedList() {
		//setup type manager
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result), 'code');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(5, $products);
		$this->assertArrayHasKey('IND00054', $products);
		$this->assertArrayHasKey('IND00043', $products);
		$this->assertArrayHasKey('IND00232', $products);
		$this->assertArrayHasKey('GFX00067', $products);
		$this->assertArrayHasKey('PHN00098', $products);
	
		$product = $products['IND00054'];
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $product);
	
		$this->assertInternalType('string', $product->getDescription());
		$this->assertEquals('Red dress', $product->getDescription());
	
		$this->assertInternalType('string', $product->getCode());
		$this->assertEquals('IND00054', $product->getCode());
	
		$this->assertInternalType('float', $product->getPrice());
		$this->assertEquals(150.65, $product->getPrice());
	
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
		
		$this->assertInstanceOf('Acme\RGBColor', $product->getColor());
	
		$result->free();
	}
	
	/**
	 * Mapping to an indexed list of a custom class using a result map
	 */
	public function testResultClassMapIndexedGroupedList() {
		//setup type manager
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap', 'Acme\Generic\GenericProduct');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultInterface($result), 'code', null, 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);

		$this->assertArrayHasKey('IND00054', $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Clothes']['IND00054']);
		$this->assertEquals('IND00054', $products['Clothes']['IND00054']->getCode());
		$this->assertArrayHasKey('IND00043', $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Clothes']['IND00043']);
		$this->assertEquals('IND00043', $products['Clothes']['IND00043']->getCode());
		$this->assertArrayHasKey('IND00232', $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Clothes']['IND00232']);
		$this->assertEquals('IND00232', $products['Clothes']['IND00232']->getCode());
		$this->assertArrayHasKey('GFX00067', $products['Hardware']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Hardware']['GFX00067']);
		$this->assertEquals('GFX00067', $products['Hardware']['GFX00067']->getCode());
		$this->assertArrayHasKey('PHN00098', $products['Smartphones']);
		$this->assertInstanceOf('Acme\Generic\GenericProduct', $products['Smartphones']['PHN00098']);
		$this->assertEquals('PHN00098', $products['Smartphones']['PHN00098']->getCode());
	
		$product = $products['Clothes']['IND00054'];
	
		$this->assertInternalType('string', $product->getDescription());
		$this->assertEquals('Red dress', $product->getDescription());
	
		$this->assertInternalType('string', $product->getCode());
		$this->assertEquals('IND00054', $product->getCode());
	
		$this->assertInternalType('float', $product->getPrice());
		$this->assertEquals(150.65, $product->getPrice());
		
		$this->assertInternalType('string', $product->getCategory());
		$this->assertEquals('Clothes', $product->getCategory());
	
		$this->assertInstanceOf('Acme\RGBColor', $product->getColor());
	
		$result->free();
	}
}
?>