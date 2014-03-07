<?php
namespace eMapper\MySQL\Result\ScalarMapper;

use eMapper\MySQL\MySQLTest;
use eMapper\Result\Mapper\ScalarTypeMapper;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Engine\MySQL\Result\MySQLResultInterface;

/**
 * Test ScalarTypeMapper with custom type columns
 * @author emaphp
 * @group mysql
 * @group result
 * @group custom
 */
class CustomTypeTest extends MySQLTest {
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
	
	public function testCustomTypeColumn() {
		$mapper = new ScalarTypeMapper(new RGBColorTypeHandler());
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 1");
		$value = $mapper->mapResult(new MySQLResultInterface($result), 'color');
		
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(225, $value->red);
		$this->assertEquals(26, $value->green);
		$this->assertEquals(26, $value->blue);
		
		$result->free();
	}
	
	public function testCustomTypeList() {
		$mapper = new ScalarTypeMapper(new RGBColorTypeHandler());
		$result = self::$conn->query("SELECT color FROM products ORDER BY product_id ASC");
		$values = $mapper->mapList(new MySQLResultInterface($result));
		
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[0]);
		$this->assertEquals(225, $values[0]->red);
		$this->assertEquals(26, $values[0]->green);
		$this->assertEquals(26, $values[0]->blue);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[1]);
		$this->assertEquals(12, $values[1]->red);
		$this->assertEquals(27, $values[1]->green);
		$this->assertEquals(217, $values[1]->blue);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[2]);
		$this->assertEquals(112, $values[2]->red);
		$this->assertEquals(124, $values[2]->green);
		$this->assertEquals(4, $values[2]->blue);
		
		$this->assertNull($values[3]);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[4]);
		$this->assertEquals(0, $values[4]->red);
		$this->assertEquals(167, $values[4]->green);
		$this->assertEquals(235, $values[4]->blue);
		
		$result->free();
	}
	
	public function testCustomTypeColumnList() {
		$mapper = new ScalarTypeMapper(new RGBColorTypeHandler());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$values = $mapper->mapList(new MySQLResultInterface($result), 'color');
		
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[0]);
		$this->assertEquals(225, $values[0]->red);
		$this->assertEquals(26, $values[0]->green);
		$this->assertEquals(26, $values[0]->blue);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[1]);
		$this->assertEquals(12, $values[1]->red);
		$this->assertEquals(27, $values[1]->green);
		$this->assertEquals(217, $values[1]->blue);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[2]);
		$this->assertEquals(112, $values[2]->red);
		$this->assertEquals(124, $values[2]->green);
		$this->assertEquals(4, $values[2]->blue);
		
		$this->assertNull($values[3]);
		
		$this->assertInstanceOf('Acme\RGBColor', $values[4]);
		$this->assertEquals(0, $values[4]->red);
		$this->assertEquals(167, $values[4]->green);
		$this->assertEquals(235, $values[4]->blue);
		
		$result->free();
	}
}
?>