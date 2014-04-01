<?php
namespace eMapper\PostgreSQL\Result\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLTest;
use eMapper\Type\Handler\IntegerTypeHandler;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;

/**
 * Test ScalarTypeMapper with integer values
 * @author emaphp
 * @group postgre
 * @group result
 * @group integer
 */
class IntegerTypeTest extends PostgreSQLTest {
	public $typeMapper;
	
	public function __construct() {
		$this->typeMapper = new ScalarTypeMapper(new IntegerTypeHandler());
	}
	
	public function testInteger() {
		$result = pg_query(self::$conn, "SELECT 2");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertEquals(2, $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT user_id FROM users WHERE user_name = 'jkirk'");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertEquals(3, $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT * FROM users WHERE user_name = 'ishmael'");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result), 'user_id');
		$this->assertEquals(5, $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT user_id FROM users ORDER BY user_id ASC");
		$value = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
		$this->assertEquals(array(1,2,3,4,5), $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT * FROM users ORDER BY user_id DESC");
		$value = $this->typeMapper->mapList(new PostgreSQLResultIterator($result), 'user_id');
		$this->assertEquals(array(5,4,3,2,1), $value);
		pg_free_result($result);
	}
	
	public function testIntegerColumn() {
		$result = pg_query(self::$conn, "SELECT * FROM sales WHERE sale_id = 1");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result), 'product_id');
	
		$this->assertInternalType('integer', $value);
		$this->assertEquals(5, $value);
	
		pg_free_result($result);
	}
	
	public function testIntegerList() {
		$result = pg_query(self::$conn, "SELECT user_id FROM sales ORDER BY sale_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
	
		pg_free_result($result);
	}
	
	public function testIntegerColumnList() {
		$result = pg_query(self::$conn, "SELECT * FROM sales ORDER BY sale_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result), 'user_id');
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
	
		pg_free_result($result);
	}
}

?>