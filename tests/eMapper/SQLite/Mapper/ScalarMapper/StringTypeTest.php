<?php
namespace eMapper\SQLite\Mapper\ScalarMapper;

use eMapper\SQLite\SQLiteTest;

/**
 * Test SQLiteMapper with string values
 * @author emaphp
 * @group sqlite
 * @group mapper
 * @group string
 */
class StringTypeTest extends SQLiteTest {
	public function testString() {
		$value = self::$mapper->type('string')->query("SELECT 'hello'");
		$this->assertEquals('hello', $value);
	
		$value = self::$mapper->type('str')->query("SELECT user_name FROM users WHERE user_id = 3");
		$this->assertEquals('jkirk', $value);
	
		$value = self::$mapper->type('s', 'user_name')->query("SELECT * FROM users WHERE user_id = 5");
		$this->assertEquals('ishmael', $value);
	
		$result = self::$mapper->type('s')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('1', $result);
	
		$result = self::$mapper->type('s')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('1987-08-10', $result);
	
		$result = self::$mapper->type('s')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('2013-08-10 19:57:15', $result);
	
		$result = self::$mapper->type('s')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('12:00:00', $result);
	
		$result = self::$mapper->type('s')->query("SELECT avatar FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals(self::$blob, $result);
	
		$result = self::$mapper->type('s')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('150.65', $result);
	
		$result = self::$mapper->type('s')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('4.5', $result);
	
		$result = self::$mapper->type('s')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('0', $result);
	
		$result = self::$mapper->type('s')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('2011', $result);
	
		$result = self::$mapper->type('s')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('0.25', $result);
	}
	
	public function testStringColumn() {
		$value = self::$mapper->type('string', 'user_name')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertEquals('jdoe', $value);
	}
	
	public function testStringList() {
		$values = self::$mapper->type('string[]')->query("SELECT user_name FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertEquals('jdoe', $values[0]);
		$this->assertEquals('okenobi', $values[1]);
		$this->assertEquals('jkirk', $values[2]);
		$this->assertEquals('egoldstein', $values[3]);
		$this->assertEquals('ishmael', $values[4]);
	}
	
	public function testStringColumnList() {
		$values = self::$mapper->type('string[]', 'user_name')->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertEquals('jdoe', $values[0]);
		$this->assertEquals('okenobi', $values[1]);
		$this->assertEquals('jkirk', $values[2]);
		$this->assertEquals('egoldstein', $values[3]);
		$this->assertEquals('ishmael', $values[4]);
	}
}

?>