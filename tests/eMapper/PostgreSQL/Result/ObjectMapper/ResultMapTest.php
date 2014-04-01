<?php
namespace eMapper\PostgreSQL\Result\ObjectMapper;

use eMapper\PostgreSQL\PostgreSQLTest;
use eMapper\Engine\PostgreSQL\Type\PostgreSQLTypeManager;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Result\Mapper\ObjectTypeMapper;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;

/**
 * Test ObjectTypeMapper class with different types of results using a result map
 * @author emaphp
 * @group result
 * @group postgre
 */
class ResultMapTest extends PostgreSQLTest {
	public $typeManager;
	
	public function __construct() {
		$this->typeManager = new PostgreSQLTypeManager();
		$this->typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	}
	
	public function testRow() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Result\UserResultMap');
		$result = pg_query(self::$conn, "SELECT * FROM users WHERE user_id = 1");
		$user = $mapper->mapResult(new PostgreSQLResultIterator($result));
	
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
	
		pg_free_result($result);
	}
	
	public function testList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Result\UserResultMap');
		$result = pg_query(self::$conn, "SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new PostgreSQLResultIterator($result));
	
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
	
		pg_free_result($result);
	}
	
	public function testIndexedList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Result\UserResultMap');
		$result = pg_query(self::$conn, "SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new PostgreSQLResultIterator($result), 'user_id');
	
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
	
		pg_free_result($result);
	}
	
	public function testCustomIndexList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Result\UserResultMap');
		$result = pg_query(self::$conn, "SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new PostgreSQLResultIterator($result), 'user_id', 's');
	
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
	
		pg_free_result($result);
	}
	
	public function testOverrideIndexList() {
		$typeManager = $this->typeManager;
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
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
		$this->assertEquals('PHN00098', $product->code);
	
		pg_free_result($result);
	}
	
	public function testGroupedList() {
		$typeManager = $this->typeManager;
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
	
		$mapper = new ObjectTypeMapper($typeManager, 'Acme\Result\GenericProductResultMap');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), null, null, 'category');
	
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
	
		pg_free_result($result);
	}
	
	public function testGroupedIndexedList() {
		$mapper = new ObjectTypeMapper($this->typeManager, 'Acme\Result\GenericProductResultMap');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), 'code', null, 'category');
	
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
	
		pg_free_result($result);
	}
}

?>