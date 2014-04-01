<?php
namespace eMapper\PostgreSQL\Result\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLTest;
use Acme\Type\RGBColorTypeHandler;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;

/**
 * Test ScalarTypeMapper with custom type columns
 * @author emaphp
 * @group postgre
 * @group result
 * @group custom
 */
class CustomTypeTest extends PostgreSQLTest {
	public $typeMapper;
	
	public function __construct() {
		$this->typeMapper = new ScalarTypeMapper(new RGBColorTypeHandler());
	}
	
	public function testCustomType() {
		$result = pg_query(self::$conn, "SELECT 'FF00ff'");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
	
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(255, $value->red);
		$this->assertEquals(0, $value->green);
		$this->assertEquals(255, $value->blue);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT color FROM products WHERE product_id = 1");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(225, $value->red);
		$this->assertEquals(26, $value->green);
		$this->assertEquals(26, $value->blue);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT color FROM products ORDER BY product_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertInstanceOf('Acme\RGBColor', $values[0]);
		$this->assertEquals(225, $values[0]->red);
		$this->assertEquals(26, $values[0]->green);
		$this->assertEquals(26, $values[0]->blue);
		pg_free_result($result);
	}
	
	public function testCustomTypeColumn() {
		$result = pg_query(self::$conn, "SELECT * FROM products WHERE product_id = 1");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result), 'color');
	
		$this->assertInstanceOf('Acme\RGBColor', $value);
		$this->assertEquals(225, $value->red);
		$this->assertEquals(26, $value->green);
		$this->assertEquals(26, $value->blue);
	
		pg_free_result($result);
	}
	
	public function testCustomTypeList() {
		$result = pg_query(self::$conn, "SELECT color FROM products ORDER BY product_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
	
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
	
		pg_free_result($result);
	}
	
	public function testCustomTypeColumnList() {
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result), 'color');
	
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
	
		pg_free_result($result);
	}
}


?>