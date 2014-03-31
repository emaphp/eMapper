<?php
namespace eMapper\MySQL\Result\ScalarMapper;

use eMapper\MySQL\MySQLTest;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\Handler\FloatTypeHandler;
use eMapper\Engine\MySQL\Result\MySQLResultIterator;

/**
 * Test ScalarTypeMapper with float values
 * 
 * @author emaphp
 * @group mysql
 * @group result
 * @group float
 */
class FloatTypeTest extends MySQLTest {
	public function testFloat() {
		$mapper = new ScalarTypeMapper(new FloatTypeHandler());
		$result = self::$conn->query("SELECT 2.5");
		$value = $mapper->mapResult(new MySQLResultIterator($result));
		$this->assertEquals(2.5, $value);
		$result->free();
	
		$result = self::$conn->query("SELECT price FROM products WHERE product_id = 3");
		$value = $mapper->mapResult(new MySQLResultIterator($result));
		$this->assertEquals(70.9, $value);
		$result->free();
	
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 5");
		$value = $mapper->mapResult(new MySQLResultIterator($result), 'price');
		$this->assertEquals(300.3, $value);
		$result->free();
	
		$result = self::$conn->query("SELECT price FROM products ORDER BY product_id ASC");
		$value = $mapper->mapList(new MySQLResultIterator($result));
		$this->assertEquals(array(150.65, 235.7, 70.9, 120.75, 300.3), $value);
		$result->free();
	
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id DESC");
		$value = $mapper->mapList(new MySQLResultIterator($result), 'price');
		$this->assertEquals(array(300.3, 120.75, 70.9, 235.7, 150.65), $value);
		$result->free();
	}
	
	public function testFloatColumn() {
		$mapper = new ScalarTypeMapper(new FloatTypeHandler());
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 1");
		$value = $mapper->mapResult(new MySQLResultIterator($result), 'price');
		
		$this->assertInternalType('float', $value);
		$this->assertEquals(150.65, $value);
		
		$result->free();
	}
	
	public function testFloatList() {
		$mapper = new ScalarTypeMapper(new FloatTypeHandler());
		$result = self::$conn->query("SELECT rating FROM products ORDER BY product_id ASC");
		$values = $mapper->mapList(new MySQLResultIterator($result));
		
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
		
		$this->assertEquals(4.5, $values[0]);
		$this->assertEquals(3.9, $values[1]);
		$this->assertEquals(4.1, $values[2]);
		$this->assertEquals(3.8, $values[3]);
		$this->assertEquals(4.8, $values[4]);
		
		$result->free();
	}
	
	public function testFloatColumnList() {
		$mapper = new ScalarTypeMapper(new FloatTypeHandler());
		$result = self::$conn->query("SELECT * FROM sales ORDER BY sale_id ASC");
		$values = $mapper->mapList(new MySQLResultIterator($result), 'discount');
		
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
		
		$this->assertEquals(.25, $values[0]);
		$this->assertEquals(.15, $values[1]);
		$this->assertEquals(.12, $values[2]);
		$this->assertEquals(.1, $values[3]);
		
		$result->free();
	}
}
?>