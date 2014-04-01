<?php
namespace eMapper\PostgreSQL\Result\ScalarMapper;

use eMapper\Type\Handler\StringTypeHandler;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;
use eMapper\PostgreSQL\PostgreSQLTest;

/**
 * Test ScalarTypeMapper with string type columns
 * @author emaphp
 * @group postgre
 * @group result
 * @group string
 */
class StringTypeTest extends PostgreSQLTest {
	public $typeMapper;
	
	public function __construct() {
		$this->typeMapper = new ScalarTypeMapper(new StringTypeHandler());
	}
	
	public function testString() {
		$result = pg_query(self::$conn, "SELECT 'hello'");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertEquals('hello', $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT user_name FROM users WHERE user_id = 3");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result));
		$this->assertEquals('jkirk', $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT * FROM users WHERE user_id = 5");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result), 'user_name');
		$this->assertEquals('ishmael', $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT user_name FROM users ORDER BY user_id ASC");
		$value = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
		$this->assertEquals(array('jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael'), $value);
		pg_free_result($result);
	
		$result = pg_query(self::$conn, "SELECT * FROM users ORDER BY user_id DESC");
		$value = $this->typeMapper->mapList(new PostgreSQLResultIterator($result), 'user_name');
		$this->assertEquals(array('ishmael', 'egoldstein', 'jkirk', 'okenobi', 'jdoe'), $value);
		pg_free_result($result);
	}
	
	public function testStringColumn() {
		$result = pg_query(self::$conn, "SELECT * FROM users WHERE user_id = 1");
		$value = $this->typeMapper->mapResult(new PostgreSQLResultIterator($result), 'user_name');
	
		$this->assertEquals('jdoe', $value);
	
		pg_free_result($result);
	}
	
	public function testStringList() {
		$result = pg_query(self::$conn, "SELECT user_name FROM users ORDER BY user_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result));
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertEquals('jdoe', $values[0]);
		$this->assertEquals('okenobi', $values[1]);
		$this->assertEquals('jkirk', $values[2]);
		$this->assertEquals('egoldstein', $values[3]);
		$this->assertEquals('ishmael', $values[4]);
	
		pg_free_result($result);
	}
	
	public function testStringColumnList() {
		$result = pg_query(self::$conn, "SELECT * FROM users ORDER BY user_id ASC");
		$values = $this->typeMapper->mapList(new PostgreSQLResultIterator($result), 'user_name');
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertEquals('jdoe', $values[0]);
		$this->assertEquals('okenobi', $values[1]);
		$this->assertEquals('jkirk', $values[2]);
		$this->assertEquals('egoldstein', $values[3]);
		$this->assertEquals('ishmael', $values[4]);
	
		pg_free_result($result);
	}
}

?>