<?php
namespace eMapper\MySQL\Result;

use eMapper\MySQL\MySQLTest;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\Handler\IntegerTypeHandler;
use eMapper\Type\Handler\StringTypeHandler;
use eMapper\Type\Handler\FloatTypeHandler;
use eMapper\Type\Handler\BooleanTypeHandler;
use eMapper\Type\Handler\DatetimeTypeHandler;
use eMapper\Engine\MySQL\Result\MySQLResultInterface;
use Acme\Type\RGBColorTypeHandler;

/**
 * Test ScalarTypeMapper with different results
 * 
 * @author emaphp
 * @group mysql
 * @group result
 */
class ScalarResultTest extends MySQLTest {
	public function testString() {
		$mapper = new ScalarTypeMapper(new StringTypeHandler());
		$result = self::$conn->query("SELECT 'hello'");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertEquals('hello', $value);
		$result->free();
		
		$result = self::$conn->query("SELECT user_name FROM users WHERE user_id = 3");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertEquals('jkirk', $value);
		$result->free();
		
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 5");
		$value = $mapper->mapResult(new MySQLResultInterface($result), 'user_name');
		$this->assertEquals('ishmael', $value);
		$result->free();
		
		$result = self::$conn->query("SELECT user_name FROM users ORDER BY user_id ASC");
		$value = $mapper->mapList(new MySQLResultInterface($result));
		$this->assertEquals(array('jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael'), $value);
		$result->free();
		
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id DESC");
		$value = $mapper->mapList(new MySQLResultInterface($result), 'user_name');
		$this->assertEquals(array('ishmael', 'egoldstein', 'jkirk', 'okenobi', 'jdoe'), $value);
		$result->free();
	}
	
	public function testInteger() {
		$mapper = new ScalarTypeMapper(new IntegerTypeHandler());
		$result = self::$conn->query("SELECT 2");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertEquals(2, $value);
		$result->free();
		
		$result = self::$conn->query("SELECT user_id FROM users WHERE user_name = 'jkirk'");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertEquals(3, $value);
		$result->free();
		
		$result = self::$conn->query("SELECT * FROM users WHERE user_name = 'ishmael'");
		$value = $mapper->mapResult(new MySQLResultInterface($result), 'user_id');
		$this->assertEquals(5, $value);
		$result->free();
		
		$result = self::$conn->query("SELECT user_id FROM users ORDER BY user_id ASC");
		$value = $mapper->mapList(new MySQLResultInterface($result));
		$this->assertEquals(array(1,2,3,4,5), $value);
		$result->free();
		
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id DESC");
		$value = $mapper->mapList(new MySQLResultInterface($result), 'user_id');
		$this->assertEquals(array(5,4,3,2,1), $value);
		$result->free();
	}
	
	public function testFloat() {
		$mapper = new ScalarTypeMapper(new FloatTypeHandler());
		$result = self::$conn->query("SELECT 2.5");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertEquals(2.5, $value);
		$result->free();
		
		$result = self::$conn->query("SELECT price FROM products WHERE product_id = 3");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertEquals(70.9, $value);
		$result->free();
		
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 5");
		$value = $mapper->mapResult(new MySQLResultInterface($result), 'price');
		$this->assertEquals(300.3, $value);
		$result->free();
		
		$result = self::$conn->query("SELECT price FROM products ORDER BY product_id ASC");
		$value = $mapper->mapList(new MySQLResultInterface($result));
		$this->assertEquals(array(150.65, 235.7, 70.9, 120.75, 300.3), $value);
		$result->free();
		
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id DESC");
		$value = $mapper->mapList(new MySQLResultInterface($result), 'price');
		$this->assertEquals(array(300.3, 120.75, 70.9, 235.7, 150.65), $value);
		$result->free();
	}
	
	public function testBoolean() {
		$mapper = new ScalarTypeMapper(new BooleanTypeHandler());
		$result = self::$conn->query("SELECT TRUE");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertTrue($value);
		$result->free();
		
		$result = self::$conn->query("SELECT FALSE");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertFalse($value);
		$result->free();
		
		$result = self::$conn->query("SELECT refurbished FROM products WHERE product_id = 1");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertFalse($value);
		$result->free();
		
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 5");
		$value = $mapper->mapResult(new MySQLResultInterface($result), 'refurbished');
		$this->assertTrue($value);
		$result->free();
		
		$result = self::$conn->query("SELECT refurbished FROM products ORDER BY product_id ASC");
		$value = $mapper->mapList(new MySQLResultInterface($result));
		$this->assertEquals(array(false, false, false, false, true), $value);
		$result->free();
		
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id DESC");
		$value = $mapper->mapList(new MySQLResultInterface($result), 'refurbished');
		$this->assertEquals(array(true, false, false, false, false), $value);
		$result->free();
	}
	
	public function testDatetime() {
		$mapper = new ScalarTypeMapper(new DatetimeTypeHandler(new \DateTimeZone('America/Argentina/Buenos_Aires')));
		$result = self::$conn->query("SELECT NOW()");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertInstanceOf('DateTime', $value);
		$this->assertRegExp('/([\d]{4})-([\d]{2})-([\d]{2}) ([\d]{2}):([\d]{2}):([\d]{2})/', $value->format('Y-m-d H:i:s'));
		
		$result = self::$conn->query("SELECT last_login FROM users WHERE user_id = 1");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertInstanceOf('DateTime', $value);
		$this->assertEquals('2013-08-10 19:57:15', $value->format('Y-m-d H:i:s'));
		
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 3");
		$value = $mapper->mapResult(new MySQLResultInterface($result), 'last_login');
		$this->assertInstanceOf('DateTime', $value);
		$this->assertEquals('2013-02-16 20:00:33', $value->format('Y-m-d H:i:s'));
		
		$result = self::$conn->query("SELECT last_login FROM users ORDER BY user_id ASC");
		$value = $mapper->mapList(new MySQLResultInterface($result));
		$this->assertInternalType('array', $value);
		$this->assertCount(5, $value);
		
		$values = array();
		
		foreach ($value as $dt) {
			$this->assertInstanceOf('DateTime', $dt);
			$values[] = $dt->format('Y-m-d H:i:s');
		}
		
		$this->assertEquals(array('2013-08-10 19:57:15', '2013-01-06 12:34:10', '2013-02-16 20:00:33', '2013-03-26 10:01:45', '2013-05-22 14:23:32'), $values);
		
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id DESC");
		$value = $mapper->mapList(new MySQLResultInterface($result), 'last_login');
		$this->assertInternalType('array', $value);
		$this->assertCount(5, $value);
		
		$values = array();
		
		foreach ($value as $dt) {
			$this->assertInstanceOf('DateTime', $dt);
			$values[] = $dt->format('Y-m-d H:i:s');
		}
		
		$this->assertEquals(array('2013-05-22 14:23:32', '2013-03-26 10:01:45', '2013-02-16 20:00:33', '2013-01-06 12:34:10', '2013-08-10 19:57:15'), $values);
	}
	
	public function testCustomType() {
		$mapper = new ScalarTypeMapper(new RGBColorTypeHandler());
		$result = self::$conn->query("SELECT 'FF00ff'");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(255, $value->red);
		$this->assertEquals(0, $value->green);
		$this->assertEquals(255, $value->blue);
		$result->free();
		
		$result = self::$conn->query("SELECT color FROM products WHERE product_id = 1");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(225, $value->red);
		$this->assertEquals(26, $value->green);
		$this->assertEquals(26, $value->blue);
		$result->free();
		
		$result = self::$conn->query("SELECT color FROM products ORDER BY product_id ASC");
		$values = $mapper->mapList(new MySQLResultInterface($result));
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[0]);
		$this->assertEquals(225, $values[0]->red);
		$this->assertEquals(26, $values[0]->green);
		$this->assertEquals(26, $values[0]->blue);
		$result->free();
	}
}
?>