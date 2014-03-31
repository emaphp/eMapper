<?php
namespace eMapper\MySQL\Result\ScalarMapper;

use eMapper\MySQL\MySQLTest;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\Handler\BooleanTypeHandler;
use eMapper\Engine\MySQL\Result\MySQLResultIterator;

/**
 * Test ScalarTypeMapper with boolean values
 * @author emaphp
 * @group mysql
 * @group result
 * @group boolean
 */
class BooleanTypeTest extends MySQLTest {
	public function testBoolean() {
		$mapper = new ScalarTypeMapper(new BooleanTypeHandler());
		$result = self::$conn->query("SELECT TRUE");
		$value = $mapper->mapResult(new MySQLResultIterator($result));
		$this->assertTrue($value);
		$result->free();
	
		$result = self::$conn->query("SELECT FALSE");
		$value = $mapper->mapResult(new MySQLResultIterator($result));
		$this->assertFalse($value);
		$result->free();
	
		$result = self::$conn->query("SELECT refurbished FROM products WHERE product_id = 1");
		$value = $mapper->mapResult(new MySQLResultIterator($result));
		$this->assertFalse($value);
		$result->free();
	
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 5");
		$value = $mapper->mapResult(new MySQLResultIterator($result), 'refurbished');
		$this->assertTrue($value);
		$result->free();
	
		$result = self::$conn->query("SELECT refurbished FROM products ORDER BY product_id ASC");
		$value = $mapper->mapList(new MySQLResultIterator($result));
		$this->assertEquals(array(false, false, false, false, true), $value);
		$result->free();
	
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id DESC");
		$value = $mapper->mapList(new MySQLResultIterator($result), 'refurbished');
		$this->assertEquals(array(true, false, false, false, false), $value);
		$result->free();
	}
	
	public function testBooleanColumn() {
		$mapper = new ScalarTypeMapper(new BooleanTypeHandler());
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 1");
		$value = $mapper->mapResult(new MySQLResultIterator($result), 'refurbished');
		
		$this->assertInternalType('boolean', $value);
		$this->assertFalse($value);
		
		$result->free();
	}
	
	public function testBooleanList() {
		$mapper = new ScalarTypeMapper(new BooleanTypeHandler());
		$result = self::$conn->query("SELECT refurbished FROM products ORDER BY product_id ASC");
		$values = $mapper->mapList(new MySQLResultIterator($result));
		
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
		
		$this->assertFalse($values[0]);
		$this->assertFalse($values[1]);
		$this->assertFalse($values[2]);
		$this->assertFalse($values[3]);
		$this->assertTrue($values[4]);
		
		$result->free();
	}
	
	public function testBooleanColumnList() {
		$mapper = new ScalarTypeMapper(new BooleanTypeHandler());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$values = $mapper->mapList(new MySQLResultIterator($result), 'refurbished');
		
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
		
		$this->assertFalse($values[0]);
		$this->assertFalse($values[1]);
		$this->assertFalse($values[2]);
		$this->assertFalse($values[3]);
		$this->assertTrue($values[4]);
		
		$result->free();
	}
}
?>