<?php
namespace eMapper\PostgreSQL\Result\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLTest;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\Handler\BooleanTypeHandler;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;

/**
 * Test ScalarTypeMapper with boolean values
 * @author emaphp
 * @group postgre
 * @group result
 * @group boolean
 */
class BooleanTypeTest extends PostgreSQLTest {
	/**
	 * Boolean mapper
	 * @var ScalarTypeMapper
	 */
	public $typeMapper;
	
	public function __construct() {
		$this->typeMapper = new ScalarTypeMapper(new BooleanTypeHandler());
	}
	
	public function testBoolean() {
		$result = pg_query(self::$conn, "SELECT TRUE");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertTrue($value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT FALSE");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertFalse($value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT refurbished FROM products WHERE product_id = 1");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertFalse($value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT * FROM products WHERE product_id = 5");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result), 'refurbished');
		$this->assertTrue($value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT refurbished FROM products ORDER BY product_id ASC");
		$value = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
		$this->assertEquals(array(false, false, false, false, true), $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id DESC");
		$value = $this->typeMapper->mapList(new PostgreSQLResultIterator($result), 'refurbished');
		$this->assertEquals(array(true, false, false, false, false), $value);
		pg_free_result($result);
	}
	
	public function testBooleanColumn() {
		$result = pg_query(self::$conn, "SELECT * FROM products WHERE product_id = 1");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result), 'refurbished');
	
		$this->assertInternalType('boolean', $value);
		$this->assertFalse($value);
	
		pg_free_result($result);
	}
	
	public function testBooleanList() {
		$result = pg_query(self::$conn, "SELECT refurbished FROM products ORDER BY product_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertFalse($values[0]);
		$this->assertFalse($values[1]);
		$this->assertFalse($values[2]);
		$this->assertFalse($values[3]);
		$this->assertTrue($values[4]);
	
		pg_free_result($result);
	}
	
	public function testBooleanColumnList() {
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result), 'refurbished');
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertFalse($values[0]);
		$this->assertFalse($values[1]);
		$this->assertFalse($values[2]);
		$this->assertFalse($values[3]);
		$this->assertTrue($values[4]);
	
		pg_free_result($result);
	}
}

?>