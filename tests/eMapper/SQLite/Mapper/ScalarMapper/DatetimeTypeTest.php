<?php
namespace eMapper\SQLite\Mapper\ScalarMapper;

use eMapper\SQLite\SQLiteTest;

/**
 * Test SQLiteMapper with date values
 * @author emaphp
 * @group sqlite
 * @group mapper
 * @group date
 */
class DatetimeTypeTest extends SQLiteTest {
	public function testDatetime() {
		$value = self::$mapper->type('DateTime')->query("SELECT date('now')");
		$this->assertInstanceOf('DateTime', $value);
		$this->assertRegExp('/([\d]{4})-([\d]{2})-([\d]{2}) ([\d]{2}):([\d]{2}):([\d]{2})/', $value->format('Y-m-d H:i:s'));
	
		$value = self::$mapper->type('dt')->query("SELECT last_login FROM users WHERE user_id = 1");
		$this->assertInstanceOf('DateTime', $value);
		$this->assertEquals('2013-08-10 19:57:15', $value->format('Y-m-d H:i:s'));
	
		$value = self::$mapper->type('date', 'last_login')->query("SELECT * FROM users WHERE user_id = 3");
		$this->assertInstanceOf('DateTime', $value);
		$this->assertEquals('2013-02-16 20:00:33', $value->format('Y-m-d H:i:s'));
	
		$result = self::$mapper->type('timestamp')->query("SELECT sale_date FROM sales WHERE sale_id = 1");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('2013-08-10 20:37:18', $result->format('Y-m-d H:i:s'));
	
		///WARNING!!!!!! 2011 -> 20:11
		$result = self::$mapper->type('DateTime')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('20:11', $result->format('H:i'));
	
		$result = self::$mapper->type('DateTime')->query("SELECT newsletter_time FROM users WHERE user_id = 3");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('17:00:00', $result->format('H:i:s'));
	}
	
	public function testDatetimeColumn() {
		$value = self::$mapper->type('DateTime', 'sale_date')->query("SELECT * FROM sales WHERE sale_id = 2");
	
		$this->assertInstanceOf('\DateTime', $value);
		$this->assertEquals('2013-05-17 14:22:50', $value->format('Y-m-d H:i:s'));
	}
	
	public function testDatetimeList() {
		$values = self::$mapper->type('DateTime[]')->query("SELECT birth_date FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertInstanceOf('\DateTime', $values[0]);
		$this->assertEquals('1987-08-10', $values[0]->format('Y-m-d'));
	
		$this->assertInstanceOf('\DateTime', $values[1]);
		$this->assertEquals('1976-03-03', $values[1]->format('Y-m-d'));
	
		$this->assertInstanceOf('\DateTime', $values[2]);
		$this->assertEquals('1967-11-21', $values[2]->format('Y-m-d'));
	
		$this->assertInstanceOf('\DateTime', $values[3]);
		$this->assertEquals('1980-12-07', $values[3]->format('Y-m-d'));
	
		$this->assertInstanceOf('\DateTime', $values[4]);
		$this->assertEquals('1977-03-16', $values[4]->format('Y-m-d'));
	}
	
	public function testDatetimeColumnList() {
		$values = self::$mapper->type('DateTime[]', 'last_login')->query("SELECT * FROM users ORDER BY user_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertInstanceOf('\DateTime', $values[0]);
		$this->assertEquals('2013-08-10 19:57:15', $values[0]->format('Y-m-d H:i:s'));
	
		$this->assertInstanceOf('\DateTime', $values[1]);
		$this->assertEquals('2013-01-06 12:34:10', $values[1]->format('Y-m-d H:i:s'));
	
		$this->assertInstanceOf('\DateTime', $values[2]);
		$this->assertEquals('2013-02-16 20:00:33', $values[2]->format('Y-m-d H:i:s'));
	
		$this->assertInstanceOf('\DateTime', $values[3]);
		$this->assertEquals('2013-03-26 10:01:45', $values[3]->format('Y-m-d H:i:s'));
	
		$this->assertInstanceOf('\DateTime', $values[4]);
		$this->assertEquals('2013-05-22 14:23:32', $values[4]->format('Y-m-d H:i:s'));
	}
	
	/**
	 * @expectedException \eMapper\Exception\MapperException
	 */
	public function testDatetimeError1() {
		$result = self::$mapper->type('DateTime')->query("SELECT user_id FROM users WHERE user_id = 1");
	}
	
	/**
	 * @expectedException \eMapper\Exception\MapperException
	 */
	public function testDatetimeError2() {
		$result = self::$mapper->type('DateTime')->query("SELECT user_name FROM users WHERE user_id = 1");
	}
	
	/**
	 * @expectedException \eMapper\Exception\MapperException
	 */
	public function testDatetimeError3() {
		$result = self::$mapper->type('DateTime')->query("SELECT avatar FROM users WHERE user_id = 1");
	}
}

?>