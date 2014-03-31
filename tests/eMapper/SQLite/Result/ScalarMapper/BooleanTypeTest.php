<?php
namespace eMapper\SQLite\Result\ScalarMapper;

use eMapper\SQLite\SQLiteTest;
use eMapper\Engine\SQLite\Result\SQLiteResultIterator;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\Handler\BooleanTypeHandler;

/**
 * Tests ScalarTypeMapper with boolean values
 * @author emaphp
 * @group sqlite
 * @group result
 * @group boolean
 */
class BooleanTypeTest extends SQLiteTest {
	public function testBoolean() {
		$mapper = new ScalarTypeMapper(new BooleanTypeHandler());
		$result = self::$conn->query("SELECT 1");
		$value = $mapper->mapResult(new SQLiteResultIterator($result));
		$this->assertTrue($value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT 0");
		$value = $mapper->mapResult(new SQLiteResultIterator($result));
		$this->assertFalse($value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT refurbished FROM products WHERE product_id = 1");
		$value = $mapper->mapResult(new SQLiteResultIterator($result));
		$this->assertFalse($value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 5");
		$value = $mapper->mapResult(new SQLiteResultIterator($result), 'refurbished');
		$this->assertTrue($value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT refurbished FROM products ORDER BY product_id ASC");
		$value = $mapper->mapList(new SQLiteResultIterator($result));
		$this->assertEquals(array(false, false, false, false, true), $value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id DESC");
		$value = $mapper->mapList(new SQLiteResultIterator($result), 'refurbished');
		$this->assertEquals(array(true, false, false, false, false), $value);
		$result->finalize();
	}
	
	public function testBooleanColumn() {
		$mapper = new ScalarTypeMapper(new BooleanTypeHandler());
		$result = self::$conn->query("SELECT * FROM products WHERE product_id = 1");
		$value = $mapper->mapResult(new SQLiteResultIterator($result), 'refurbished');
	
		$this->assertInternalType('boolean', $value);
		$this->assertFalse($value);
	
		$result->finalize();
	}
	
	public function testBooleanList() {
		$mapper = new ScalarTypeMapper(new BooleanTypeHandler());
		$result = self::$conn->query("SELECT refurbished FROM products ORDER BY product_id ASC");
		$values = $mapper->mapList(new SQLiteResultIterator($result));
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertFalse($values[0]);
		$this->assertFalse($values[1]);
		$this->assertFalse($values[2]);
		$this->assertFalse($values[3]);
		$this->assertTrue($values[4]);
	
		$result->finalize();
	}
	
	public function testBooleanColumnList() {
		$mapper = new ScalarTypeMapper(new BooleanTypeHandler());
		$result = self::$conn->query("SELECT * FROM products ORDER BY product_id ASC");
		$values = $mapper->mapList(new SQLiteResultIterator($result), 'refurbished');
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertFalse($values[0]);
		$this->assertFalse($values[1]);
		$this->assertFalse($values[2]);
		$this->assertFalse($values[3]);
		$this->assertTrue($values[4]);
	
		$result->finalize();
	}
}
?>