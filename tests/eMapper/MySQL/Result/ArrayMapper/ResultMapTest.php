<?php
namespace eMapper\MySQL\Result\ArrayMapper;

use eMapper\MySQL\MySQLTest;
use eMapper\Result\Mapper\ArrayMapper;
use eMapper\Engine\MySQL\Result\MySQLResultIterator;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Result\ArrayType;
use eMapper\Engine\MySQL\Type\MySQLTypeManager;

/**
 * Test ArrayMapper class with various results
 * 
 * @author emaphp
 * @group mysql
 * @group result
 */
class ResultMapTest extends MySQLTest {
	public function testArrayTypeError() {
		$mapper = new ArrayMapper(new MySQLTypeManager(), 'Acme\Result\UserResultMap');
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 3");
		$user = $mapper->mapResult(new MySQLResultIterator($result), ArrayType::NUM);
		$this->assertInternalType('array', $user);
	}
	
	/**
	 * Obtains a row with all fields declared through a result map
	 */
	public function testResultMapRow() {
		$mapper = new ArrayMapper(new MySQLTypeManager(), 'Acme\Result\UserResultMap');
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 3");
		$user = $mapper->mapResult(new MySQLResultIterator($result));
		$this->assertInternalType('array', $user);
		
		$this->assertArrayNotHasKey(0, $user);
		$this->assertArrayNotHasKey(1, $user);
		$this->assertArrayNotHasKey(2, $user);
		$this->assertArrayNotHasKey(3, $user);
		$this->assertArrayNotHasKey(4, $user);
		$this->assertArrayNotHasKey(5, $user);
				
		$this->assertArrayHasKey('user_id', $user);
		$this->assertInternalType('integer', $user['user_id']);
		$this->assertEquals(3, $user['user_id']);
		
		$this->assertArrayHasKey('name', $user);
		$this->assertInternalType('string', $user['name']);
		$this->assertEquals('jkirk', $user['name']);
		
		$this->assertArrayHasKey('lastLogin', $user);
		$this->assertInternalType('string', $user['lastLogin']);
		$this->assertEquals('2013-02-16 20:00:33', $user['lastLogin']);
		
		$result->free();
	}
	
	public function testList() {
		$mapper = new ArrayMapper(new MySQLTypeManager(), 'Acme\Result\UserResultMap');
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new MySQLResultIterator($result));
		
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		
		$this->assertArrayHasKey('user_id', $users[0]);
		$this->assertInternalType('integer', $users[0]['user_id']);
		$this->assertEquals(1, $users[0]['user_id']);
		
		$this->assertArrayHasKey('name', $users[0]);
		$this->assertInternalType('string', $users[0]['name']);
		$this->assertEquals('jdoe', $users[0]['name']);
		
		$this->assertArrayHasKey('lastLogin', $users[0]);
		$this->assertInternalType('string', $users[0]['lastLogin']);
		$this->assertEquals('2013-08-10 19:57:15', $users[0]['lastLogin']);
		
		$result->free();
	}
	
	public function testIndexedList() {
		$mapper = new ArrayMapper(new MySQLTypeManager(), 'Acme\Result\UserResultMap');
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new MySQLResultIterator($result), 'name');
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey('jdoe', $users);
		$this->assertArrayHasKey('okenobi', $users);
		$this->assertArrayHasKey('jkirk', $users);
		$this->assertArrayHasKey('egoldstein', $users);
		$this->assertArrayHasKey('ishmael', $users);
	
		$user = $users['jdoe'];
		$this->assertArrayHasKey('user_id', $user);
		$this->assertInternalType('integer', $user['user_id']);
		$this->assertEquals(1, $user['user_id']);
	
		$this->assertArrayHasKey('name', $user);
		$this->assertInternalType('string', $user['name']);
		$this->assertEquals('jdoe', $user['name']);
	
		$this->assertArrayHasKey('lastLogin', $user);
		$this->assertInternalType('string', $user['lastLogin']);
		$this->assertEquals('2013-08-10 19:57:15', $user['lastLogin']);
		
		$result->free();
	}
	
	/**
	 * Test setting a custom index to a list using a result map
	 */
	public function testCustomIndexResultMapList() {
		$mapper = new ArrayMapper(new MySQLTypeManager(), 'Acme\Result\UserResultMap');
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new MySQLResultIterator($result), 'user_id', 'string');
		
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
		
		$this->assertArrayHasKey('user_id', $users['1']);
		$this->assertInternalType('integer', $users['1']['user_id']);
		$this->assertEquals(1, $users['1']['user_id']);
		
		$this->assertArrayHasKey('name', $users['1']);
		$this->assertInternalType('string', $users['1']['name']);
		$this->assertEquals('jdoe', $users['1']['name']);

		$this->assertArrayHasKey('lastLogin', $users['1']);
		$this->assertInternalType('string', $users['1']['lastLogin']);
		$this->assertEquals('2013-08-10 19:57:15', $users['1']['lastLogin']);
		
		$result->free();
	}
	
	public function testIndexOverrideList() {
		$typeManager = new MySQLTypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
		$mapper = new ArrayMapper($typeManager, 'Acme\Result\GenericProductResultMap');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultIterator($result), 'category');
		
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		
		//
		$this->assertArrayHasKey('code', $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['code']);
		$this->assertEquals('IND00232', $products['Clothes']['code']);
		
		$this->assertArrayHasKey('description', $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['description']);
		$this->assertEquals('Green shirt', $products['Clothes']['description']);
		
		$this->assertArrayHasKey('color', $products['Clothes']);
		$this->assertInstanceOf('Acme\RGBColor', $products['Clothes']['color']);
		
		$this->assertArrayHasKey('price', $products['Clothes']);
		$this->assertInternalType('float', $products['Clothes']['price']);
		$this->assertEquals(70.9, $products['Clothes']['price']);
		
		$this->assertArrayHasKey('category', $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['category']);
		$this->assertEquals('Clothes', $products['Clothes']['category']);
		
		$result->free();
	}
	
	public function testGroupedList() {
		$typeManager = new MySQLTypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
		$mapper = new ArrayMapper($typeManager, 'Acme\Result\GenericProductResultMap');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultIterator($result), null, null, 'category');
		
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		
		$this->assertCount(3, $products['Clothes']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertCount(1, $products['Smartphones']);
		
		////
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Hardware']);
		$this->assertArrayHasKey(0, $products['Smartphones']);
		
		$this->assertArrayHasKey('code', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['code']);
		$this->assertEquals('IND00232', $products['Clothes'][2]['code']);
		
		$this->assertArrayHasKey('description', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['description']);
		$this->assertEquals('Green shirt', $products['Clothes'][2]['description']);
		
		$this->assertArrayHasKey('color', $products['Clothes'][2]);
		$this->assertInstanceOf('Acme\RGBColor', $products['Clothes'][2]['color']);
		
		$this->assertArrayHasKey('price', $products['Clothes'][2]);
		$this->assertInternalType('float', $products['Clothes'][2]['price']);
		$this->assertEquals(70.9, $products['Clothes'][2]['price']);
		
		$this->assertArrayHasKey('category', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['category']);
		$this->assertEquals('Clothes', $products['Clothes'][2]['category']);
		
		$result->free();
	}
	
	public function testGroupedIndexedList() {
		$typeManager = new MySQLTypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
		$mapper = new ArrayMapper($typeManager, 'Acme\Result\GenericProductResultMap');
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new MySQLResultIterator($result), 'code', null, 'category');
		
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
		
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		
		$this->assertCount(3, $products['Clothes']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertCount(1, $products['Smartphones']);
		
		$this->assertArrayHasKey('IND00054', $products['Clothes']);
		$this->assertArrayHasKey('IND00043', $products['Clothes']);
		$this->assertArrayHasKey('IND00232', $products['Clothes']);
		$this->assertArrayHasKey('GFX00067', $products['Hardware']);
		$this->assertArrayHasKey('PHN00098', $products['Smartphones']);
		
		$this->assertArrayHasKey('code', $products['Clothes']['IND00232']);
		$this->assertInternalType('string', $products['Clothes']['IND00232']['code']);
		$this->assertEquals('IND00232', $products['Clothes']['IND00232']['code']);
		
		$this->assertArrayHasKey('description', $products['Clothes']['IND00232']);
		$this->assertInternalType('string', $products['Clothes']['IND00232']['description']);
		$this->assertEquals('Green shirt', $products['Clothes']['IND00232']['description']);
		
		$this->assertArrayHasKey('color', $products['Clothes']['IND00232']);
		$this->assertInstanceOf('Acme\RGBColor', $products['Clothes']['IND00232']['color']);
		
		$this->assertArrayHasKey('price', $products['Clothes']['IND00232']);
		$this->assertInternalType('float', $products['Clothes']['IND00232']['price']);
		$this->assertEquals(70.9, $products['Clothes']['IND00232']['price']);
		
		$this->assertArrayHasKey('category', $products['Clothes']['IND00232']);
		$this->assertInternalType('string', $products['Clothes']['IND00232']['category']);
		$this->assertEquals('Clothes', $products['Clothes']['IND00232']['category']);
		
		$result->free();
	}
}
?>