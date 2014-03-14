<?php
namespace eMapper\SQLite\Result\ScalarMapper;

use eMapper\SQLite\SQLiteTest;
use eMapper\Engine\SQLite\Result\SQLiteResultInterface;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\Handler\IntegerTypeHandler;

class IntegerTypeTest extends SQLiteTest {
	public function testInteger() {
		$mapper = new ScalarTypeMapper(new IntegerTypeHandler());
		$result = self::$conn->query("SELECT 2");
		$value = $mapper->mapResult(new SQLiteResultInterface($result));
		$this->assertEquals(2, $value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT user_id FROM users WHERE user_name = 'jkirk'");
		$value = $mapper->mapResult(new SQLiteResultInterface($result));
		$this->assertEquals(3, $value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT * FROM users WHERE user_name = 'ishmael'");
		$value = $mapper->mapResult(new SQLiteResultInterface($result), 'user_id');
		$this->assertEquals(5, $value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT user_id FROM users ORDER BY user_id ASC");
		$value = $mapper->mapList(new SQLiteResultInterface($result));
		$this->assertEquals(array(1,2,3,4,5), $value);
		$result->finalize();
	
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id DESC");
		$value = $mapper->mapList(new SQLiteResultInterface($result), 'user_id');
		$this->assertEquals(array(5,4,3,2,1), $value);
		$result->finalize();
	}
	
	public function testIntegerColumn() {
		$mapper = new ScalarTypeMapper(new IntegerTypeHandler());
		$result = self::$conn->query("SELECT * FROM sales WHERE sale_id = 1");
		$value = $mapper->mapResult(new SQLiteResultInterface($result), 'product_id');
	
		$this->assertInternalType('integer', $value);
		$this->assertEquals(5, $value);
	
		$result->finalize();
	}
	
	public function testIntegerList() {
		$mapper = new ScalarTypeMapper(new IntegerTypeHandler());
		$result = self::$conn->query("SELECT user_id FROM sales ORDER BY sale_id ASC");
		$values = $mapper->mapList(new SQLiteResultInterface($result));
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
	
		$result->finalize();
	}
	
	public function testIntegerColumnList() {
		$mapper = new ScalarTypeMapper(new IntegerTypeHandler());
		$result = self::$conn->query("SELECT * FROM sales ORDER BY sale_id ASC");
		$values = $mapper->mapList(new SQLiteResultInterface($result), 'user_id');
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
	
		$result->finalize();
	}
}
?>