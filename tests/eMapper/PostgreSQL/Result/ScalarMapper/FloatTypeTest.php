<?php
namespace eMapper\PostgreSQL\Result\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLTest;
use eMapper\Type\Handler\FloatTypeHandler;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;

/**
 * Test ScalarTypeMapper with float values
 *
 * @author emaphp
 * @group postgre
 * @group result
 * @group float
 */
class FloatTypeTest extends PostgreSQLTest {
	public $typeMapper;
	
	public function __construct() {
		$this->typeMapper = new ScalarTypeMapper(new FloatTypeHandler());
	}
	
	public function testFloat() {
		$result = pg_query(self::$conn, "SELECT 2.5");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertEquals(2.5, $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT price FROM products WHERE product_id = 3");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertEquals(70.9, $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT * FROM products WHERE product_id = 5");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result), 'price');
		$this->assertEquals(300.3, $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT price FROM products ORDER BY product_id ASC");
		$value = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
		$this->assertEquals(array(150.65, 235.7, 70.9, 120.75, 300.3), $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id DESC");
		$value = $this->typeMapper->mapList(new PostgreSQLResultIterator($result), 'price');
		$this->assertEquals(array(300.3, 120.75, 70.9, 235.7, 150.65), $value);
		pg_free_result($result);
	}
	
	public function testFloatColumn() {
		$result = pg_query(self::$conn, "SELECT * FROM products WHERE product_id = 1");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result), 'price');
	
		$this->assertInternalType('float', $value);
		$this->assertEquals(150.65, $value);
	
		pg_free_result($result);
	}
	
	public function testFloatList() {
		$result = pg_query(self::$conn, "SELECT rating FROM products ORDER BY product_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertEquals(4.5, $values[0]);
		$this->assertEquals(3.9, $values[1]);
		$this->assertEquals(4.1, $values[2]);
		$this->assertEquals(3.8, $values[3]);
		$this->assertEquals(4.8, $values[4]);
	
		pg_free_result($result);
	}
	
	public function testFloatColumnList() {
		$result = pg_query(self::$conn, "SELECT * FROM sales ORDER BY sale_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result), 'discount');
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(.25, $values[0]);
		$this->assertEquals(.15, $values[1]);
		$this->assertEquals(.12, $values[2]);
		$this->assertEquals(.1, $values[3]);
	
		pg_free_result($result);
	}
}

?>