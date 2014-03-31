<?php
namespace eMapper\MySQL\Result\ScalarMapper;

use eMapper\MySQL\MySQLTest;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\Handler\IntegerTypeHandler;
use eMapper\Engine\MySQL\Result\MySQLResultIterator;

/**
 * Test ScalarTypeMapper with integer values
 * @author emaphp
 * @group mysql
 * @group result
 * @group integer
 */
class IntegerTypeTest extends MySQLTest {
	public function testInteger() {
		$mapper = new ScalarTypeMapper(new IntegerTypeHandler());
		$result = self::$conn->query("SELECT 2");
		$value = $mapper->mapResult(new MySQLResultIterator($result));
		$this->assertEquals(2, $value);
		$result->free();
	
		$result = self::$conn->query("SELECT user_id FROM users WHERE user_name = 'jkirk'");
		$value = $mapper->mapResult(new MySQLResultIterator($result));
		$this->assertEquals(3, $value);
		$result->free();
	
		$result = self::$conn->query("SELECT * FROM users WHERE user_name = 'ishmael'");
		$value = $mapper->mapResult(new MySQLResultIterator($result), 'user_id');
		$this->assertEquals(5, $value);
		$result->free();
	
		$result = self::$conn->query("SELECT user_id FROM users ORDER BY user_id ASC");
		$value = $mapper->mapList(new MySQLResultIterator($result));
		$this->assertEquals(array(1,2,3,4,5), $value);
		$result->free();
	
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id DESC");
		$value = $mapper->mapList(new MySQLResultIterator($result), 'user_id');
		$this->assertEquals(array(5,4,3,2,1), $value);
		$result->free();
	}
	
	public function testIntegerColumn() {
		$mapper = new ScalarTypeMapper(new IntegerTypeHandler());
		$result = self::$conn->query("SELECT * FROM sales WHERE sale_id = 1");
		$value = $mapper->mapResult(new MySQLResultIterator($result), 'product_id');
		
		$this->assertInternalType('integer', $value);
		$this->assertEquals(5, $value);
		
		$result->free();
	}
	
	public function testIntegerList() {
		$mapper = new ScalarTypeMapper(new IntegerTypeHandler());
		$result = self::$conn->query("SELECT user_id FROM sales ORDER BY sale_id ASC");
		$values = $mapper->mapList(new MySQLResultIterator($result));
		
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
		
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
		
		$result->free();
	}
	
	public function testIntegerColumnList() {
		$mapper = new ScalarTypeMapper(new IntegerTypeHandler());
		$result = self::$conn->query("SELECT * FROM sales ORDER BY sale_id ASC");
		$values = $mapper->mapList(new MySQLResultIterator($result), 'user_id');
		
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
		
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
		
		$result->free();
	}
}
?>