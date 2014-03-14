<?php
namespace eMapper\SQLite\Result\ScalarMapper;

use eMapper\SQLite\SQLiteTest;
use eMapper\Engine\SQLite\Result\SQLiteResultInterface;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\Handler\StringTypeHandler;

/**
 * Tests ScalarTypeMapper with string values
 * @author emaphp
 * @group sqlite
 * @group result
 * @group string
 */
class StringTypeTest extends SQLiteTest {
	public function testString() {
		$mapper = new ScalarTypeMapper(new StringTypeHandler());
		$result = self::$conn->query("SELECT 'hello'");
		$value = $mapper->mapResult(new SQLiteResultInterface($result));
		$this->assertEquals('hello', $value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT user_name FROM users WHERE user_id = 3");
		$value = $mapper->mapResult(new SQLiteResultInterface($result));
		$this->assertEquals('jkirk', $value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 5");
		$value = $mapper->mapResult(new SQLiteResultInterface($result), 'user_name');
		$this->assertEquals('ishmael', $value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT user_name FROM users ORDER BY user_id ASC");
		$value = $mapper->mapList(new SQLiteResultInterface($result));
		$this->assertEquals(array('jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael'), $value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id DESC");
		$value = $mapper->mapList(new SQLiteResultInterface($result), 'user_name');
		$this->assertEquals(array('ishmael', 'egoldstein', 'jkirk', 'okenobi', 'jdoe'), $value);
		$result->finalize();
	}
	
	public function testStringColumn() {
		$mapper = new ScalarTypeMapper(new StringTypeHandler());
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 1");
		$value = $mapper->mapResult(new SQLiteResultInterface($result), 'user_name');
	
		$this->assertEquals('jdoe', $value);
	
		$result->finalize();
	}
	
	public function testStringList() {
		$mapper = new ScalarTypeMapper(new StringTypeHandler());
		$result = self::$conn->query("SELECT user_name FROM users ORDER BY user_id ASC");
		$values = $mapper->mapList(new SQLiteResultInterface($result));
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertEquals('jdoe', $values[0]);
		$this->assertEquals('okenobi', $values[1]);
		$this->assertEquals('jkirk', $values[2]);
		$this->assertEquals('egoldstein', $values[3]);
		$this->assertEquals('ishmael', $values[4]);
	
		$result->finalize();
	}
	
	public function testStringColumnList() {
		$mapper = new ScalarTypeMapper(new StringTypeHandler());
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$values = $mapper->mapList(new SQLiteResultInterface($result), 'user_name');
	
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
	
		$this->assertEquals('jdoe', $values[0]);
		$this->assertEquals('okenobi', $values[1]);
		$this->assertEquals('jkirk', $values[2]);
		$this->assertEquals('egoldstein', $values[3]);
		$this->assertEquals('ishmael', $values[4]);
	
		$result->finalize();
	}
}
?>