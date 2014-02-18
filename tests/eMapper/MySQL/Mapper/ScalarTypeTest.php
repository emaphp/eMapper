<?php
namespace eMapper\MySQL\Mapper;

use eMapper\MySQL\MySQLTest;

/**
 * 
 * @author emaphp
 * @group mysql
 */
class ScalarTypeTest extends MySQLTest {
	public function __construct() {
		self::setUpBeforeClass();
	}
	
	/**
	 * Obtains various types of values as an integer
	 */
	public function testInteger() {
		$result = self::$mapper->type('integer')->query("SELECT NULL");
		$this->assertNull($result);
		
		$result = self::$mapper->type('integer')->query("SELECT 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(1, $result);
		
		$result = self::$mapper->type('int')->query("SELECT 2");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(2, $result);
		
		$result = self::$mapper->type('i')->query("SELECT 3");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(3, $result);
		
		$result = self::$mapper->type('i')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(1, $result);
		
		$result = self::$mapper->type('i')->query("SELECT user_name FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(0, $result);
		
		$result = self::$mapper->type('i')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(1987, $result);
		
		$result = self::$mapper->type('i')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(2013, $result);
		
		$result = self::$mapper->type('i')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(12, $result);
		
		$result = self::$mapper->type('i')->query("SELECT avatar FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(0, $result);
		
		$result = self::$mapper->type('i')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(150, $result);
		
		$result = self::$mapper->type('i')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(4, $result);
		
		$result = self::$mapper->type('i')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(0, $result);
		
		$result = self::$mapper->type('i')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(2011, $result);
		
		$result = self::$mapper->type('i')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(0, $result);
	}
	
	/**
	 * Obtains various types of values as float
	 */
	public function testFloat() {
		$result = self::$mapper->type('float')->query("SELECT NULL");
		$this->assertNull($result);
		
		$result = self::$mapper->type('float')->query("SELECT 0.25");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0.25, $result);
		
		$result = self::$mapper->type('double')->query("SELECT 0.5");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0.5, $result);
		
		$result = self::$mapper->type('real')->query("SELECT 0.75");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0.75, $result);
		
		$result = self::$mapper->type('f')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(1.0, $result);
		
		$result = self::$mapper->type('f')->query("SELECT user_name FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0, $result);
		
		$result = self::$mapper->type('f')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(1987, $result);
		
		$result = self::$mapper->type('f')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(2013, $result);
		
		$result = self::$mapper->type('f')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(12, $result);
		
		$result = self::$mapper->type('f')->query("SELECT avatar FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0, $result);
		
		$result = self::$mapper->type('f')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('float', $result);
		$this->assertEquals(150.65, $result);
		
		$result = self::$mapper->type('f')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('float', $result);
		$this->assertEquals(4.5, $result);
		
		$result = self::$mapper->type('f')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0, $result);
		
		$result = self::$mapper->type('f')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('float', $result);
		$this->assertEquals(2011, $result);
		
		$result = self::$mapper->type('f')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('float', $result);
		$this->assertEquals(0.25, $result);
	}
	
	/**
	 * Obtains various types of values as string
	 */
	public function testString() {
		$result = self::$mapper->type('string')->query("SELECT NULL");
		$this->assertNull($result);
		
		$result = self::$mapper->type('string')->query("SELECT 'hello'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('hello', $result);
		
		$result = self::$mapper->type('str')->query("SELECT 'world'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('world', $result);
		
		$result = self::$mapper->type('s')->query("SELECT ''");
		$this->assertInternalType('string', $result);
		$this->assertEquals('', $result);
		
		$result = self::$mapper->type('s')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('1', $result);
		
		$result = self::$mapper->type('s')->query("SELECT user_name FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('jdoe', $result);
		
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
	
	/**
	 * Obtains various types of values as an unquoted string
	 */
	public function testUnquotedString() {
		$result = self::$mapper->type('ustring')->query("SELECT NULL");
		$this->assertNull($result);
		
		$result = self::$mapper->type('ustring')->query("SELECT 'hello'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('hello', $result);
		
		$result = self::$mapper->type('ustr')->query("SELECT 'world'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('world', $result);
		
		$result = self::$mapper->type('us')->query("SELECT ''");
		$this->assertInternalType('string', $result);
		$this->assertEquals('', $result);
		
		$result = self::$mapper->type('us')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('1', $result);
		
		$result = self::$mapper->type('us')->query("SELECT user_name FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('jdoe', $result);
		
		$result = self::$mapper->type('us')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('1987-08-10', $result);
		
		$result = self::$mapper->type('us')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('2013-08-10 19:57:15', $result);
		
		$result = self::$mapper->type('us')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('12:00:00', $result);
		
		$result = self::$mapper->type('us')->query("SELECT avatar FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals(file_get_contents(__DIR__ . '/../../avatar.gif'), $result);
		
		$result = self::$mapper->type('us')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('150.65', $result);
		
		$result = self::$mapper->type('us')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('4.5', $result);
		
		$result = self::$mapper->type('us')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('0', $result);
		
		$result = self::$mapper->type('us')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('2011', $result);
		
		$result = self::$mapper->type('us')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('0.25', $result);
	}
	
	/**
	 * Obtains various types of values as boolean
	 */
	public function testBoolean() {
		$result = self::$mapper->type('boolean')->query("SELECT NULL");
		$this->assertNull($result);
		
		$result = self::$mapper->type('boolean')->query("SELECT TRUE");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('boolean')->query("SELECT FALSE");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
		
		$result = self::$mapper->type('bool')->query("SELECT 'T'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('bool')->query("SELECT 'F'");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
		
		$result = self::$mapper->type('b')->query("SELECT 't'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT 'f'");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
		
		$result = self::$mapper->type('b')->query("SELECT 'true'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT 'false'");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
		
		$result = self::$mapper->type('b')->query("SELECT 'TRUE'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT 'FALSE'");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
		
		$result = self::$mapper->type('b')->query("SELECT 0");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
		
		$result = self::$mapper->type('b')->query("SELECT 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT user_name FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT avatar FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('boolean', $result);
		$this->assertFalse($result);
		
		$result = self::$mapper->type('b')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
		
		$result = self::$mapper->type('b')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('boolean', $result);
		$this->assertTrue($result);
	}
	
	/**
	 * Obtains various types of values as a binary blob
	 */
	public function testBlob() {
		$result = self::$mapper->type('blob')->query("SELECT NULL");
		$this->assertNull($result);
		
		$result = self::$mapper->type('blob')->query("SELECT 'hello'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('hello', $result);
		
		$result = self::$mapper->type('bin')->query("SELECT 'world'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('world', $result);
		
		$result = self::$mapper->type('x')->query("SELECT ''");
		$this->assertInternalType('string', $result);
		$this->assertEquals('', $result);
		
		$result = self::$mapper->type('x')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('1', $result);
		
		$result = self::$mapper->type('x')->query("SELECT user_name FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('jdoe', $result);
		
		$result = self::$mapper->type('x')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('1987-08-10', $result);
		
		$result = self::$mapper->type('x')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('2013-08-10 19:57:15', $result);
		
		$result = self::$mapper->type('x')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('12:00:00', $result);
		
		$result = self::$mapper->type('x')->query("SELECT avatar FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals(self::$blob, $result);
		
		$result = self::$mapper->type('x')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('150.65', $result);
		
		$result = self::$mapper->type('x')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('4.5', $result);
		
		$result = self::$mapper->type('x')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('0', $result);
		
		$result = self::$mapper->type('x')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('2011', $result);
		
		$result = self::$mapper->type('x')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('0.25', $result);
	}
	
	/**
	 * Obtains various types of values as a Datetime instance
	 */
	public function testDatetime() {
		$result = self::$mapper->type('DateTime')->query("SELECT NULL");
		$this->assertNull($result);
		
		$result = self::$mapper->type('DateTime')->query("SELECT newsletter_time FROM users WHERE user_id = 3");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('17:00:00', $result->format('H:i:s'));
		
		$result = self::$mapper->type('datetime')->query("SELECT NOW()");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals(call_user_func(array(new \DateTime(), 'format'), 'Y-m-d'), $result->format('Y-m-d'));
		
		$result = self::$mapper->type('dt')->query("SELECT birth_date FROM users WHERE user_id = 1");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('1987-08-10', $result->format('Y-m-d'));
		
		$result = self::$mapper->type('date')->query("SELECT last_login FROM users WHERE user_id = 1");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('2013-08-10 19:57:15', $result->format('Y-m-d H:i:s'));
		
		$result = self::$mapper->type('timestamp')->query("SELECT sale_date FROM sales WHERE sale_id = 1");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('2013-08-10 20:37:18', $result->format('Y-m-d H:i:s'));
		
		///WARNING!!!!!! 2011 -> 20:11
		$result = self::$mapper->type('DateTime')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('20:11', $result->format('H:i'));
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
	
	/**
	 * @expectedException \eMapper\Exception\MapperException
	 */
	public function testDatetimeError4() {
		$result = self::$mapper->type('DateTime')->query("SELECT price FROM products WHERE product_id = 1");	
	}
	
	public function testCustomType() {
		$result = self::$mapper->type('Acme\RGBColor')->query("SELECT 'ffFF00'");
		
		$this->assertInstanceOf('Acme\RGBColor', $result);
		$this->assertEquals(255, $result->red);
		$this->assertEquals(255, $result->green);
		$this->assertEquals(0, $result->blue);
		
		$result = self::$mapper->type('Acme\RGBColor')->query("SELECT color FROM products WHERE product_id = 1");
		$this->assertInstanceOf('Acme\RGBColor', $result);
		$this->assertEquals(225, $result->red);
		$this->assertEquals(26, $result->green);
		$this->assertEquals(26, $result->blue);
		
		$colors = self::$mapper->type('Acme\RGBColor[]')->query("SELECT color FROM products ORDER BY product_id ASC");
		$this->assertInternalType('array', $colors);
		$this->assertCount(5, $colors);
		$this->assertInstanceOf('Acme\RGBColor', $colors[0]);
		$this->assertEquals(225, $colors[0]->red);
		$this->assertEquals(26, $colors[0]->green);
		$this->assertEquals(26, $colors[0]->blue);
		
		//usig alias
		$result = self::$mapper->type('color')->query("SELECT 'ffFF00'");
		
		$this->assertInstanceOf('Acme\RGBColor', $result);
		$this->assertEquals(255, $result->red);
		$this->assertEquals(255, $result->green);
		$this->assertEquals(0, $result->blue);
		
		$result = self::$mapper->type('color')->query("SELECT color FROM products WHERE product_id = 1");
		$this->assertInstanceOf('Acme\RGBColor', $result);
		$this->assertEquals(225, $result->red);
		$this->assertEquals(26, $result->green);
		$this->assertEquals(26, $result->blue);
		
		$colors = self::$mapper->type('color[]')->query("SELECT color FROM products ORDER BY product_id ASC");
		$this->assertInternalType('array', $colors);
		$this->assertCount(5, $colors);
		$this->assertInstanceOf('Acme\RGBColor', $colors[0]);
		$this->assertEquals(225, $colors[0]->red);
		$this->assertEquals(26, $colors[0]->green);
		$this->assertEquals(26, $colors[0]->blue);
	}
}
?>