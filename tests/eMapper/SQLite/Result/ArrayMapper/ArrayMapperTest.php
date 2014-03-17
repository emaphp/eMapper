<?php
namespace eMapper\SQLite\Result\ArrayMapper;

use eMapper\SQLite\SQLiteTest;
use eMapper\Engine\SQLite\Result\SQLiteResultInterface;
use eMapper\Result\Mapper\ArrayTypeMapper;
use eMapper\Type\TypeManager;

/**
 * Tests ArrayTypeMapper with various types of result
 * @author emaphp
 * @group sqlite
 * @group result
 */
class ArrayMapperTest extends SQLiteTest {
	/**
	 * Obtains a row as different types of arrays
	 */
	public function testRow() {
		//SQLITE3_BOTH
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 1");
		$user = $mapper->mapResult(new SQLiteResultInterface($result));
		$this->assertInternalType('array', $user);
		$this->assertArrayHasKey(0, $user);
		$this->assertArrayHasKey(1, $user);
		$this->assertArrayHasKey(2, $user);
		$this->assertArrayHasKey(3, $user);
		$this->assertArrayHasKey(4, $user);
		$this->assertArrayHasKey(5, $user);
	
		$this->assertInternalType('integer', $user[0]);
		$this->assertEquals(1, $user[0]);
		$this->assertInternalType('string', $user[1]);
		$this->assertEquals('jdoe', $user[1]);
		$this->assertInternalType('string', $user[2]);
		$this->assertEquals('1987-08-10', $user[2]);
		$this->assertInternalType('string', $user[3]);
		$this->assertEquals('2013-08-10 19:57:15', $user[3]);
		$this->assertInternalType('string', $user[4]);
		$this->assertEquals('12:00:00', $user[4]);
		$this->assertInternalType('string', $user[5]);
		$this->assertEquals(self::$blob, $user[5]);
	
		$this->assertArrayHasKey('user_id', $user);
		$this->assertArrayHasKey('user_name', $user);
		$this->assertArrayHasKey('birth_date', $user);
		$this->assertArrayHasKey('last_login', $user);
		$this->assertArrayHasKey('newsletter_time', $user);
		$this->assertArrayHasKey('avatar', $user);
	
		$this->assertInternalType('integer', $user['user_id']);
		$this->assertEquals(1, $user['user_id']);
		$this->assertInternalType('string', $user['user_name']);
		$this->assertEquals('jdoe', $user['user_name']);
		$this->assertInternalType('string', $user['birth_date']);
		$this->assertEquals('1987-08-10', $user['birth_date']);
		$this->assertInternalType('string', $user['last_login']);
		$this->assertEquals('2013-08-10 19:57:15', $user['last_login']);
		$this->assertInternalType('string', $user['newsletter_time']);
		$this->assertEquals('12:00:00', $user['newsletter_time']);
		$this->assertInternalType('string', $user['avatar']);
		$this->assertEquals(self::$blob, $user['avatar']);
		$result->finalize();
	
		//SQLITE3_ASSOC
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 1");
		$user = $mapper->mapResult(new SQLiteResultInterface($result), SQLITE3_ASSOC);
		$this->assertInternalType('array', $user);
		$this->assertArrayNotHasKey(0, $user);
		$this->assertArrayNotHasKey(1, $user);
		$this->assertArrayNotHasKey(2, $user);
		$this->assertArrayNotHasKey(3, $user);
		$this->assertArrayNotHasKey(4, $user);
		$this->assertArrayNotHasKey(5, $user);
	
		$this->assertArrayHasKey('user_id', $user);
		$this->assertArrayHasKey('user_name', $user);
		$this->assertArrayHasKey('birth_date', $user);
		$this->assertArrayHasKey('last_login', $user);
		$this->assertArrayHasKey('newsletter_time', $user);
		$this->assertArrayHasKey('avatar', $user);
	
		$result->finalize();
	
		//SQLITE3_NUM
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 1");
		$user = $mapper->mapResult(new SQLiteResultInterface($result), SQLITE3_NUM);
		$this->assertInternalType('array', $user);
		$this->assertArrayHasKey(0, $user);
		$this->assertArrayHasKey(1, $user);
		$this->assertArrayHasKey(2, $user);
		$this->assertArrayHasKey(3, $user);
		$this->assertArrayHasKey(4, $user);
		$this->assertArrayHasKey(5, $user);
		$this->assertArrayNotHasKey('user_id', $user);
		$this->assertArrayNotHasKey('user_name', $user);
		$this->assertArrayNotHasKey('birth_date', $user);
		$this->assertArrayNotHasKey('last_login', $user);
		$this->assertArrayNotHasKey('newsletter_time', $user);
		$this->assertArrayNotHasKey('avatar', $user);
	
		$result->finalize();
	}
	
	/**
	 * Obtains a list of various types of arrays
	 */
	public function testList() {
		//SQLITE3_BOTH
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new SQLiteResultInterface($result));
	
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
		$this->assertArrayHasKey('user_name', $users[0]);
		$this->assertInternalType('string', $users[0]['user_name']);
		$this->assertEquals('jdoe', $users[0]['user_name']);
		$this->assertArrayHasKey('birth_date', $users[0]);
		$this->assertInternalType('string', $users[0]['birth_date']);
		$this->assertEquals('1987-08-10', $users[0]['birth_date']);
		$this->assertArrayHasKey('last_login', $users[0]);
		$this->assertInternalType('string', $users[0]['last_login']);
		$this->assertEquals('2013-08-10 19:57:15', $users[0]['last_login']);
		$this->assertArrayHasKey('newsletter_time', $users[0]);
		$this->assertInternalType('string', $users[0]['newsletter_time']);
		$this->assertEquals('12:00:00', $users[0]['newsletter_time']);
		$this->assertArrayHasKey('avatar', $users[0]);
		$this->assertInternalType('string', $users[0]['avatar']);
		$this->assertEquals(self::$blob, $users[0]['avatar']);
	
		$this->assertArrayHasKey(0, $users[0]);
		$this->assertInternalType('integer', $users[0][0]);
		$this->assertEquals(1, $users[0][0]);
		$this->assertArrayHasKey(1, $users[0]);
		$this->assertInternalType('string', $users[0][1]);
		$this->assertEquals('jdoe', $users[0][1]);
		$this->assertArrayHasKey(2, $users[0]);
		$this->assertInternalType('string', $users[0][2]);
		$this->assertEquals('1987-08-10', $users[0][2]);
		$this->assertArrayHasKey(3, $users[0]);
		$this->assertInternalType('string', $users[0][3]);
		$this->assertEquals('2013-08-10 19:57:15', $users[0][3]);
		$this->assertArrayHasKey(4, $users[0]);
		$this->assertInternalType('string', $users[0][4]);
		$this->assertEquals('12:00:00', $users[0][4]);
		$this->assertArrayHasKey(5, $users[0]);
		$this->assertInternalType('string', $users[0][5]);
		$this->assertEquals(self::$blob, $users[0][5]);
	
		$result->finalize();
	
		//SQLITE3_NUM
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new SQLiteResultInterface($result), null, null, null, null, SQLITE3_NUM);
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$this->assertArrayNotHasKey('user_id', $users[0]);
		$this->assertArrayNotHasKey('user_name', $users[0]);
		$this->assertArrayNotHasKey('birth_date', $users[0]);
		$this->assertArrayNotHasKey('last_login', $users[0]);
		$this->assertArrayNotHasKey('newsletter_time', $users[0]);
		$this->assertArrayNotHasKey('avatar', $users[0]);
	
		$this->assertArrayHasKey(0, $users[0]);
		$this->assertArrayHasKey(1, $users[0]);
		$this->assertArrayHasKey(2, $users[0]);
		$this->assertArrayHasKey(3, $users[0]);
		$this->assertArrayHasKey(4, $users[0]);
		$this->assertArrayHasKey(5, $users[0]);
	
		$result->finalize();
	
		//SQLITE3_ASSOC
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new SQLiteResultInterface($result), null, null, null, null, SQLITE3_ASSOC);
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$this->assertArrayNotHasKey(0, $users[0]);
		$this->assertArrayNotHasKey(1, $users[0]);
		$this->assertArrayNotHasKey(2, $users[0]);
		$this->assertArrayNotHasKey(3, $users[0]);
		$this->assertArrayNotHasKey(4, $users[0]);
		$this->assertArrayNotHasKey(5, $users[0]);
	
		$this->assertArrayHasKey('user_id', $users[0]);
		$this->assertArrayHasKey('user_name', $users[0]);
		$this->assertArrayHasKey('birth_date', $users[0]);
		$this->assertArrayHasKey('last_login', $users[0]);
		$this->assertArrayHasKey('newsletter_time', $users[0]);
		$this->assertArrayHasKey('avatar', $users[0]);
	
		$result->finalize();
	}
	
	/**
	 * Obtains an indexed list by a integer/string column
	 */
	public function testIndexedList() {
		//SQLITE3_BOTH
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new SQLiteResultInterface($result), 'user_id', 'integer');
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$this->assertArrayHasKey('user_id', $users[1]);
		$this->assertInternalType('integer', $users[1]['user_id']);
		$this->assertEquals(1, $users[1]['user_id']);
		$this->assertArrayHasKey('user_name', $users[1]);
		$this->assertInternalType('string', $users[1]['user_name']);
		$this->assertEquals('jdoe', $users[1]['user_name']);
		$this->assertArrayHasKey('birth_date', $users[1]);
		$this->assertInternalType('string', $users[1]['birth_date']);
		$this->assertEquals('1987-08-10', $users[1]['birth_date']);
		$this->assertArrayHasKey('last_login', $users[1]);
		$this->assertInternalType('string', $users[1]['last_login']);
		$this->assertEquals('2013-08-10 19:57:15', $users[1]['last_login']);
		$this->assertArrayHasKey('newsletter_time', $users[1]);
		$this->assertInternalType('string', $users[1]['newsletter_time']);
		$this->assertEquals('12:00:00', $users[1]['newsletter_time']);
		$this->assertArrayHasKey('avatar', $users[1]);
		$this->assertInternalType('string', $users[1]['avatar']);
		$this->assertEquals(self::$blob, $users[1]['avatar']);
	
		$this->assertArrayHasKey(0, $users[1]);
		$this->assertInternalType('integer', $users[1][0]);
		$this->assertEquals(1, $users[1][0]);
		$this->assertArrayHasKey(1, $users[1]);
		$this->assertInternalType('string', $users[1][1]);
		$this->assertEquals('jdoe', $users[1][1]);
		$this->assertArrayHasKey(2, $users[1]);
		$this->assertInternalType('string', $users[1][2]);
		$this->assertEquals('1987-08-10', $users[1][2]);
		$this->assertArrayHasKey(3, $users[1]);
		$this->assertInternalType('string', $users[1][3]);
		$this->assertEquals('2013-08-10 19:57:15', $users[1][3]);
		$this->assertArrayHasKey(4, $users[1]);
		$this->assertInternalType('string', $users[1][4]);
		$this->assertEquals('12:00:00', $users[1][4]);
		$this->assertArrayHasKey(5, $users[1]);
		$this->assertInternalType('string', $users[1][5]);
		$this->assertEquals(self::$blob, $users[1][5]);
	
		$result->finalize();
	
		//SQLITE3_ASSOC
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new SQLiteResultInterface($result), 'user_id', 'integer', null, null, SQLITE3_ASSOC);
		$this->assertInternalType('array', $users);
	
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$this->assertArrayHasKey('user_id', $users[1]);
		$this->assertArrayHasKey('user_name', $users[1]);
		$this->assertArrayHasKey('birth_date', $users[1]);
		$this->assertArrayHasKey('last_login', $users[1]);
		$this->assertArrayHasKey('newsletter_time', $users[1]);
		$this->assertArrayHasKey('avatar', $users[1]);
	
		$this->assertArrayNotHasKey(0, $users[1]);
		$this->assertArrayNotHasKey(1, $users[1]);
		$this->assertArrayNotHasKey(2, $users[1]);
		$this->assertArrayNotHasKey(3, $users[1]);
		$this->assertArrayNotHasKey(4, $users[1]);
		$this->assertArrayNotHasKey(5, $users[1]);
	
		$result->finalize();
	
		//SQLITE3_NUM
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new SQLiteResultInterface($result), 0, null, null, null, SQLITE3_NUM);
		$this->assertInternalType('array', $users);
	
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$this->assertArrayNotHasKey('user_id', $users[1]);
		$this->assertArrayNotHasKey('user_name', $users[1]);
		$this->assertArrayNotHasKey('birth_date', $users[1]);
		$this->assertArrayNotHasKey('last_login', $users[1]);
		$this->assertArrayNotHasKey('newsletter_time', $users[1]);
		$this->assertArrayNotHasKey('avatar', $users[1]);
	
		$this->assertArrayHasKey(0, $users[1]);
		$this->assertArrayHasKey(1, $users[1]);
		$this->assertArrayHasKey(2, $users[1]);
		$this->assertArrayHasKey(3, $users[1]);
		$this->assertArrayHasKey(4, $users[1]);
		$this->assertArrayHasKey(5, $users[1]);
	
		$result->finalize();
	}
	
	public function testCustomIndexList() {
		//SQLITE3_BOTH
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new SQLiteResultInterface($result), 'user_id', 'string');
	
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
		$this->assertArrayHasKey('user_name', $users['1']);
		$this->assertInternalType('string', $users['1']['user_name']);
		$this->assertEquals('jdoe', $users['1']['user_name']);
		$this->assertArrayHasKey('birth_date', $users['1']);
		$this->assertInternalType('string', $users['1']['birth_date']);
		$this->assertEquals('1987-08-10', $users['1']['birth_date']);
		$this->assertArrayHasKey('last_login', $users['1']);
		$this->assertInternalType('string', $users['1']['last_login']);
		$this->assertEquals('2013-08-10 19:57:15', $users['1']['last_login']);
		$this->assertArrayHasKey('newsletter_time', $users['1']);
		$this->assertInternalType('string', $users['1']['newsletter_time']);
		$this->assertEquals('12:00:00', $users['1']['newsletter_time']);
		$this->assertArrayHasKey('avatar', $users['1']);
		$this->assertInternalType('string', $users['1']['avatar']);
		$this->assertEquals(self::$blob, $users['1']['avatar']);
	
		$this->assertArrayHasKey(0, $users['1']);
		$this->assertInternalType('integer', $users['1'][0]);
		$this->assertEquals(1, $users['1'][0]);
		$this->assertArrayHasKey(1, $users['1']);
		$this->assertInternalType('string', $users['1'][1]);
		$this->assertEquals('jdoe', $users['1'][1]);
		$this->assertArrayHasKey(2, $users['1']);
		$this->assertInternalType('string', $users['1'][2]);
		$this->assertEquals('1987-08-10', $users['1'][2]);
		$this->assertArrayHasKey(3, $users['1']);
		$this->assertInternalType('string', $users['1'][3]);
		$this->assertEquals('2013-08-10 19:57:15', $users['1'][3]);
		$this->assertArrayHasKey(4, $users['1']);
		$this->assertInternalType('string', $users['1'][4]);
		$this->assertEquals('12:00:00', $users['1'][4]);
		$this->assertArrayHasKey(5, $users['1']);
		$this->assertInternalType('string', $users['1'][5]);
		$this->assertEquals(self::$blob, $users['1'][5]);
	
		$result->finalize();
	
		//SQLITE3_ASSOC
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new SQLiteResultInterface($result), 'user_id', 'string', null, null, SQLITE3_ASSOC);
		$this->assertInternalType('array', $users);
	
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey('user_id', $users['1']);
		$this->assertArrayHasKey('user_name', $users['1']);
		$this->assertArrayHasKey('birth_date', $users['1']);
		$this->assertArrayHasKey('last_login', $users['1']);
		$this->assertArrayHasKey('newsletter_time', $users['1']);
		$this->assertArrayHasKey('avatar', $users['1']);
		$this->assertArrayNotHasKey(0, $users['1']);
		$this->assertArrayNotHasKey(1, $users['1']);
		$this->assertArrayNotHasKey(2, $users['1']);
		$this->assertArrayNotHasKey(3, $users['1']);
		$this->assertArrayNotHasKey(4, $users['1']);
		$this->assertArrayNotHasKey(5, $users['1']);
	
		$result->finalize();
	
		//SQLITE3_NUM
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new SQLiteResultInterface($result), 0, 'string', null, null, SQLITE3_NUM);
		$this->assertInternalType('array', $users);
	
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayNotHasKey('user_id', $users['1']);
		$this->assertArrayNotHasKey('user_name', $users['1']);
		$this->assertArrayNotHasKey('birth_date', $users['1']);
		$this->assertArrayNotHasKey('last_login', $users['1']);
		$this->assertArrayNotHasKey('newsletter_time', $users['1']);
		$this->assertArrayNotHasKey('avatar', $users['1']);
	
		$this->assertArrayHasKey(0, $users['1']);
		$this->assertArrayHasKey(1, $users['1']);
		$this->assertArrayHasKey(2, $users['1']);
		$this->assertArrayHasKey(3, $users['1']);
		$this->assertArrayHasKey(4, $users['1']);
		$this->assertArrayHasKey(5, $users['1']);
	
		$result->finalize();
	}
	
	/**
	 * Test indexing by a common value
	 */
	public function testIndexOverrideList() {
		//SQLITE3_BOTH
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'category');
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
	
		////
		$this->assertArrayHasKey('product_id', $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertInternalType('integer', $products['Clothes']['product_id']);
		$this->assertEquals(3, $products['Clothes']['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['product_code']);
		$this->assertEquals('IND00232', $products['Clothes']['product_code']);
	
		$this->assertArrayHasKey('description', $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['description']);
		$this->assertEquals('Green shirt', $products['Clothes']['description']);
	
		$this->assertArrayHasKey('color', $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['color']);
		$this->assertEquals('707c04', $products['Clothes']['color']);
	
		$this->assertArrayHasKey('price', $products['Clothes']);
		$this->assertArrayHasKey(4, $products['Clothes']);
		$this->assertInternalType('float', $products['Clothes']['price']);
		$this->assertEquals(70.9, $products['Clothes']['price']);
	
		$this->assertArrayHasKey('category', $products['Clothes']);
		$this->assertArrayHasKey(5, $products['Clothes']);
		$this->assertInternalType('string', $products['Clothes']['category']);
		$this->assertEquals('Clothes', $products['Clothes']['category']);
	
		$this->assertArrayHasKey('rating', $products['Clothes']);
		$this->assertArrayHasKey(6, $products['Clothes']);
		$this->assertInternalType('float', $products['Clothes']['rating']);
		$this->assertEquals(4.1, $products['Clothes']['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Clothes']);
		$this->assertArrayHasKey(7, $products['Clothes']);
		$this->assertInternalType('integer', $products['Clothes']['refurbished']);
		$this->assertEquals(0, $products['Clothes']['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Clothes']);
		$this->assertArrayHasKey(8, $products['Clothes']);
		$this->assertInternalType('integer', $products['Clothes']['manufacture_year']);
		$this->assertEquals(2013, $products['Clothes']['manufacture_year']);
	
		////
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertArrayHasKey('product_id', $products['Hardware']);
		$this->assertInternalType('integer', $products['Hardware']['product_id']);
		$this->assertEquals(4, $products['Hardware']['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Hardware']);
		$this->assertInternalType('string', $products['Hardware']['product_code']);
		$this->assertEquals('GFX00067', $products['Hardware']['product_code']);
	
		$this->assertArrayHasKey('description', $products['Hardware']);
		$this->assertInternalType('string', $products['Hardware']['description']);
		$this->assertEquals('ATI HD 9999', $products['Hardware']['description']);
	
		$this->assertArrayHasKey('color', $products['Hardware']);
		$this->assertNull($products['Hardware']['color']);
	
		$this->assertArrayHasKey('price', $products['Hardware']);
		$this->assertInternalType('float', $products['Hardware']['price']);
		$this->assertEquals(120.75, $products['Hardware']['price']);
	
		$this->assertArrayHasKey('category', $products['Hardware']);
		$this->assertInternalType('string', $products['Hardware']['category']);
		$this->assertEquals('Hardware', $products['Hardware']['category']);
	
		$this->assertArrayHasKey('rating', $products['Hardware']);
		$this->assertInternalType('float', $products['Hardware']['rating']);
		$this->assertEquals(3.8, $products['Hardware']['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Hardware']);
		$this->assertInternalType('integer', $products['Hardware']['refurbished']);
		$this->assertEquals(0, $products['Hardware']['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Hardware']);
		$this->assertInternalType('integer', $products['Hardware']['manufacture_year']);
		$this->assertEquals(2013, $products['Hardware']['manufacture_year']);
	
		////
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertArrayHasKey('product_id', $products['Smartphones']);
		$this->assertInternalType('integer', $products['Smartphones']['product_id']);
		$this->assertEquals(5, $products['Smartphones']['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Smartphones']);
		$this->assertInternalType('string', $products['Smartphones']['product_code']);
		$this->assertEquals('PHN00098', $products['Smartphones']['product_code']);
	
		$this->assertArrayHasKey('description', $products['Smartphones']);
		$this->assertInternalType('string', $products['Smartphones']['description']);
		$this->assertEquals('Android phone', $products['Smartphones']['description']);
	
		$this->assertArrayHasKey('color', $products['Smartphones']);
		$this->assertInternalType('string', $products['Smartphones']['color']);
		$this->assertEquals('00a7eb', $products['Smartphones']['color']);
	
		$this->assertArrayHasKey('price', $products['Smartphones']);
		$this->assertInternalType('float', $products['Smartphones']['price']);
		$this->assertEquals(300.3, $products['Smartphones']['price']);
	
		$this->assertArrayHasKey('category', $products['Smartphones']);
		$this->assertInternalType('string', $products['Smartphones']['category']);
		$this->assertEquals('Smartphones', $products['Smartphones']['category']);
	
		$this->assertArrayHasKey('rating', $products['Smartphones']);
		$this->assertInternalType('float', $products['Smartphones']['rating']);
		$this->assertEquals(4.8, $products['Smartphones']['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Smartphones']);
		$this->assertInternalType('integer', $products['Smartphones']['refurbished']);
		$this->assertEquals(1, $products['Smartphones']['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Smartphones']);
		$this->assertInternalType('integer', $products['Smartphones']['manufacture_year']);
		$this->assertEquals(2011, $products['Smartphones']['manufacture_year']);
	
		$result->finalize();
	
		//SQLITE3_ASSOC
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'category', null, null, null, SQLITE3_ASSOC);
	
		$this->assertArrayHasKey('product_id', $products['Clothes']);
		$this->assertArrayHasKey('product_code', $products['Clothes']);
		$this->assertArrayHasKey('description', $products['Clothes']);
		$this->assertArrayHasKey('color', $products['Clothes']);
		$this->assertArrayHasKey('price', $products['Clothes']);
		$this->assertArrayHasKey('category', $products['Clothes']);
		$this->assertArrayHasKey('rating', $products['Clothes']);
		$this->assertArrayHasKey('refurbished', $products['Clothes']);
		$this->assertArrayHasKey('manufacture_year', $products['Clothes']);
		$this->assertArrayNotHasKey(0, $products['Clothes']);
		$this->assertArrayNotHasKey(1, $products['Clothes']);
		$this->assertArrayNotHasKey(2, $products['Clothes']);
		$this->assertArrayNotHasKey(3, $products['Clothes']);
		$this->assertArrayNotHasKey(4, $products['Clothes']);
		$this->assertArrayNotHasKey(5, $products['Clothes']);
		$this->assertArrayNotHasKey(6, $products['Clothes']);
		$this->assertArrayNotHasKey(7, $products['Clothes']);
		$this->assertArrayNotHasKey(8, $products['Clothes']);
	
		$result->finalize();
	
		//SQLITE3_NUM
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 5, null, null, null, SQLITE3_NUM);
	
		$this->assertArrayNotHasKey('product_id', $products['Clothes']);
		$this->assertArrayNotHasKey('product_code', $products['Clothes']);
		$this->assertArrayNotHasKey('description', $products['Clothes']);
		$this->assertArrayNotHasKey('color', $products['Clothes']);
		$this->assertArrayNotHasKey('price', $products['Clothes']);
		$this->assertArrayNotHasKey('category', $products['Clothes']);
		$this->assertArrayNotHasKey('rating', $products['Clothes']);
		$this->assertArrayNotHasKey('refurbished', $products['Clothes']);
		$this->assertArrayNotHasKey('manufacture_year', $products['Clothes']);
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
		$this->assertArrayHasKey(4, $products['Clothes']);
		$this->assertArrayHasKey(5, $products['Clothes']);
		$this->assertArrayHasKey(6, $products['Clothes']);
		$this->assertArrayHasKey(7, $products['Clothes']);
		$this->assertArrayHasKey(8, $products['Clothes']);
	
		$result->finalize();
	}
	
	/**
	 * Test grouping by a column
	 */
	public function testGroupedList() {
		//SQLITE3_BOTH
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), null, null, 'category');
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
	
		$this->assertArrayHasKey('product_id', $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0]['product_id']);
		$this->assertEquals(1, $products['Clothes'][0]['product_id']);
	
		$this->assertArrayHasKey(0, $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0][0]);
		$this->assertEquals(1, $products['Clothes'][0][0]);
	
		$this->assertArrayHasKey('product_code', $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0]['product_code']);
		$this->assertEquals('IND00054', $products['Clothes'][0]['product_code']);
	
		$this->assertArrayHasKey(1, $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0][1]);
		$this->assertEquals('IND00054', $products['Clothes'][0][1]);
	
		$this->assertArrayHasKey('description', $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0]['description']);
		$this->assertEquals('Red dress', $products['Clothes'][0]['description']);
	
		$this->assertArrayHasKey(2, $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0][2]);
		$this->assertEquals('Red dress', $products['Clothes'][0][2]);
	
		$this->assertArrayHasKey('color', $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0]['color']);
		$this->assertEquals('e11a1a', $products['Clothes'][0]['color']);
	
		$this->assertArrayHasKey(3, $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0][3]);
		$this->assertEquals('e11a1a', $products['Clothes'][0][3]);
	
		$this->assertArrayHasKey('price', $products['Clothes'][0]);
		$this->assertInternalType('float', $products['Clothes'][0]['price']);
		$this->assertEquals(150.65, $products['Clothes'][0]['price']);
	
		$this->assertArrayHasKey(4, $products['Clothes'][0]);
		$this->assertInternalType('float', $products['Clothes'][0][4]);
		$this->assertEquals(150.65, $products['Clothes'][0][4]);
	
		$this->assertArrayHasKey('category', $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0]['category']);
		$this->assertEquals('Clothes', $products['Clothes'][0]['category']);
	
		$this->assertArrayHasKey(5, $products['Clothes'][0]);
		$this->assertInternalType('string', $products['Clothes'][0][5]);
		$this->assertEquals('Clothes', $products['Clothes'][0][5]);
	
		$this->assertArrayHasKey('rating', $products['Clothes'][0]);
		$this->assertInternalType('float', $products['Clothes'][0]['rating']);
		$this->assertEquals(4.5, $products['Clothes'][0]['rating']);
	
		$this->assertArrayHasKey(6, $products['Clothes'][0]);
		$this->assertInternalType('float', $products['Clothes'][0][6]);
		$this->assertEquals(4.5, $products['Clothes'][0][6]);
	
		$this->assertArrayHasKey('refurbished', $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0]['refurbished']);
		$this->assertEquals(0, $products['Clothes'][0]['refurbished']);
	
		$this->assertArrayHasKey(7, $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0][7]);
		$this->assertEquals(0, $products['Clothes'][0][7]);
	
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0]['manufacture_year']);
		$this->assertEquals(2011, $products['Clothes'][0]['manufacture_year']);
	
		$this->assertArrayHasKey(8, $products['Clothes'][0]);
		$this->assertInternalType('integer', $products['Clothes'][0][8]);
		$this->assertEquals(2011, $products['Clothes'][0][8]);
	
		////
		$this->assertArrayHasKey('product_id', $products['Clothes'][1]);
		$this->assertInternalType('integer', $products['Clothes'][1]['product_id']);
		$this->assertEquals(2, $products['Clothes'][1]['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Clothes'][1]);
		$this->assertInternalType('string', $products['Clothes'][1]['product_code']);
		$this->assertEquals('IND00043', $products['Clothes'][1]['product_code']);
	
		$this->assertArrayHasKey('description', $products['Clothes'][1]);
		$this->assertInternalType('string', $products['Clothes'][1]['description']);
		$this->assertEquals('Blue jeans', $products['Clothes'][1]['description']);
	
		$this->assertArrayHasKey('color', $products['Clothes'][1]);
		$this->assertInternalType('string', $products['Clothes'][1]['color']);
		$this->assertEquals('0c1bd9', $products['Clothes'][1]['color']);
	
		$this->assertArrayHasKey('price', $products['Clothes'][1]);
		$this->assertInternalType('float', $products['Clothes'][1]['price']);
		$this->assertEquals(235.7, $products['Clothes'][1]['price']);
	
		$this->assertArrayHasKey('category', $products['Clothes'][1]);
		$this->assertInternalType('string', $products['Clothes'][1]['category']);
		$this->assertEquals('Clothes', $products['Clothes'][1]['category']);
	
		$this->assertArrayHasKey('rating', $products['Clothes'][1]);
		$this->assertInternalType('float', $products['Clothes'][1]['rating']);
		$this->assertEquals(3.9, $products['Clothes'][1]['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Clothes'][1]);
		$this->assertInternalType('integer', $products['Clothes'][1]['refurbished']);
		$this->assertEquals(0, $products['Clothes'][1]['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][1]);
		$this->assertInternalType('integer', $products['Clothes'][1]['manufacture_year']);
		$this->assertEquals(2012, $products['Clothes'][1]['manufacture_year']);
	
		////
		$this->assertArrayHasKey('product_id', $products['Clothes'][2]);
		$this->assertInternalType('integer', $products['Clothes'][2]['product_id']);
		$this->assertEquals(3, $products['Clothes'][2]['product_id']);
	
		$this->assertArrayHasKey('product_code', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['product_code']);
		$this->assertEquals('IND00232', $products['Clothes'][2]['product_code']);
	
		$this->assertArrayHasKey('description', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['description']);
		$this->assertEquals('Green shirt', $products['Clothes'][2]['description']);
	
		$this->assertArrayHasKey('color', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['color']);
		$this->assertEquals('707c04', $products['Clothes'][2]['color']);
	
		$this->assertArrayHasKey('price', $products['Clothes'][2]);
		$this->assertInternalType('float', $products['Clothes'][2]['price']);
		$this->assertEquals(70.9, $products['Clothes'][2]['price']);
	
		$this->assertArrayHasKey('category', $products['Clothes'][2]);
		$this->assertInternalType('string', $products['Clothes'][2]['category']);
		$this->assertEquals('Clothes', $products['Clothes'][2]['category']);
	
		$this->assertArrayHasKey('rating', $products['Clothes'][2]);
		$this->assertInternalType('float', $products['Clothes'][2]['rating']);
		$this->assertEquals(4.1, $products['Clothes'][2]['rating']);
	
		$this->assertArrayHasKey('refurbished', $products['Clothes'][2]);
		$this->assertInternalType('integer', $products['Clothes'][2]['refurbished']);
		$this->assertEquals(0, $products['Clothes'][2]['refurbished']);
	
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][2]);
		$this->assertInternalType('integer', $products['Clothes'][2]['manufacture_year']);
		$this->assertEquals(2013, $products['Clothes'][2]['manufacture_year']);
	
		////
		$this->assertArrayHasKey(0, $products['Hardware']);
	
		////
		$this->assertArrayHasKey(0, $products['Smartphones']);
	
		$result->finalize();
	
		//SQLITE3_ASSOC
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), null, null, 'category', null, SQLITE3_ASSOC);
	
		$this->assertArrayHasKey('product_id', $products['Clothes'][0]);
		$this->assertArrayHasKey('product_code', $products['Clothes'][0]);
		$this->assertArrayHasKey('description', $products['Clothes'][0]);
		$this->assertArrayHasKey('color', $products['Clothes'][0]);
		$this->assertArrayHasKey('price', $products['Clothes'][0]);
		$this->assertArrayHasKey('category', $products['Clothes'][0]);
		$this->assertArrayHasKey('rating', $products['Clothes'][0]);
		$this->assertArrayHasKey('refurbished', $products['Clothes'][0]);
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][0]);
	
		$this->assertArrayNotHasKey(0, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(1, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(2, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(3, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(4, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(5, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(6, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(7, $products['Clothes'][0]);
		$this->assertArrayNotHasKey(8, $products['Clothes'][0]);
	
		$result->finalize();
	
		//SQLITE3_NUM
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), null, null, 5, null, SQLITE3_NUM);
	
		$this->assertArrayNotHasKey('product_id', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('product_code', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('description', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('color', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('price', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('category', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('rating', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('refurbished', $products['Clothes'][0]);
		$this->assertArrayNotHasKey('manufacture_year', $products['Clothes'][0]);
	
		$this->assertArrayHasKey(0, $products['Clothes'][0]);
		$this->assertArrayHasKey(1, $products['Clothes'][0]);
		$this->assertArrayHasKey(2, $products['Clothes'][0]);
		$this->assertArrayHasKey(3, $products['Clothes'][0]);
		$this->assertArrayHasKey(4, $products['Clothes'][0]);
		$this->assertArrayHasKey(5, $products['Clothes'][0]);
		$this->assertArrayHasKey(6, $products['Clothes'][0]);
		$this->assertArrayHasKey(7, $products['Clothes'][0]);
		$this->assertArrayHasKey(8, $products['Clothes'][0]);
	
		$result->finalize();
	}
	
	/**
	 * Test grouping and indexing a list by a column
	 */
	public function testGroupedIndexedList() {
		//SQLITE3_BOTH
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'product_id', 'int', 'category');
	
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
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
		$this->assertArrayHasKey(4, $products['Hardware']);
		$this->assertArrayHasKey(5, $products['Smartphones']);
	
		////
		$this->assertArrayHasKey('product_id', $products['Clothes'][1]);
		$this->assertArrayHasKey('product_code', $products['Clothes'][1]);
		$this->assertArrayHasKey('description', $products['Clothes'][1]);
		$this->assertArrayHasKey('color', $products['Clothes'][1]);
		$this->assertArrayHasKey('price', $products['Clothes'][1]);
		$this->assertArrayHasKey('category', $products['Clothes'][1]);
		$this->assertArrayHasKey('rating', $products['Clothes'][1]);
		$this->assertArrayHasKey('refurbished', $products['Clothes'][1]);
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][1]);
	
		$this->assertArrayHasKey(0, $products['Clothes'][1]);
		$this->assertArrayHasKey(1, $products['Clothes'][1]);
		$this->assertArrayHasKey(2, $products['Clothes'][1]);
		$this->assertArrayHasKey(3, $products['Clothes'][1]);
		$this->assertArrayHasKey(4, $products['Clothes'][1]);
		$this->assertArrayHasKey(5, $products['Clothes'][1]);
		$this->assertArrayHasKey(6, $products['Clothes'][1]);
		$this->assertArrayHasKey(7, $products['Clothes'][1]);
		$this->assertArrayHasKey(8, $products['Clothes'][1]);
	
		$result->finalize();
	
		//SQLITE3_ASSOC
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 'product_id', 'i', 'category', null, SQLITE3_ASSOC);
	
		////
		$this->assertArrayHasKey('product_id', $products['Clothes'][1]);
		$this->assertArrayHasKey('product_code', $products['Clothes'][1]);
		$this->assertArrayHasKey('description', $products['Clothes'][1]);
		$this->assertArrayHasKey('color', $products['Clothes'][1]);
		$this->assertArrayHasKey('price', $products['Clothes'][1]);
		$this->assertArrayHasKey('category', $products['Clothes'][1]);
		$this->assertArrayHasKey('rating', $products['Clothes'][1]);
		$this->assertArrayHasKey('refurbished', $products['Clothes'][1]);
		$this->assertArrayHasKey('manufacture_year', $products['Clothes'][1]);
	
		$this->assertArrayNotHasKey(0, $products['Clothes'][1]);
		$this->assertArrayNotHasKey(1, $products['Clothes'][1]);
		$this->assertArrayNotHasKey(2, $products['Clothes'][1]);
		$this->assertArrayNotHasKey(3, $products['Clothes'][1]);
		$this->assertArrayNotHasKey(4, $products['Clothes'][1]);
		$this->assertArrayNotHasKey(5, $products['Clothes'][1]);
		$this->assertArrayNotHasKey(6, $products['Clothes'][1]);
		$this->assertArrayNotHasKey(7, $products['Clothes'][1]);
		$this->assertArrayNotHasKey(8, $products['Clothes'][1]);
	
		$result->finalize();
	
		//SQLITE3_NUM
		$mapper = new ArrayTypeMapper(new TypeManager());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new SQLiteResultInterface($result), 0, 'int', 5, null, SQLITE3_NUM);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertArrayHasKey(3, $products['Clothes']);
		$this->assertArrayHasKey(4, $products['Hardware']);
		$this->assertArrayHasKey(5, $products['Smartphones']);
	
		////
		$this->assertArrayNotHasKey('product_id', $products['Clothes'][1]);
		$this->assertArrayNotHasKey('product_code', $products['Clothes'][1]);
		$this->assertArrayNotHasKey('description', $products['Clothes'][1]);
		$this->assertArrayNotHasKey('color', $products['Clothes'][1]);
		$this->assertArrayNotHasKey('price', $products['Clothes'][1]);
		$this->assertArrayNotHasKey('category', $products['Clothes'][1]);
		$this->assertArrayNotHasKey('rating', $products['Clothes'][1]);
		$this->assertArrayNotHasKey('refurbished', $products['Clothes'][1]);
		$this->assertArrayNotHasKey('manufacture_year', $products['Clothes'][1]);
	
		$this->assertArrayHasKey(0, $products['Clothes'][1]);
		$this->assertArrayHasKey(1, $products['Clothes'][1]);
		$this->assertArrayHasKey(2, $products['Clothes'][1]);
		$this->assertArrayHasKey(3, $products['Clothes'][1]);
		$this->assertArrayHasKey(4, $products['Clothes'][1]);
		$this->assertArrayHasKey(5, $products['Clothes'][1]);
		$this->assertArrayHasKey(6, $products['Clothes'][1]);
		$this->assertArrayHasKey(7, $products['Clothes'][1]);
		$this->assertArrayHasKey(8, $products['Clothes'][1]);
	
		$result->finalize();
	}
}

?>