<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\Result\MySQLResultIterator;
use eMapper\Result\ResultIterator;
use eMapper\Result\ArrayType;

/**
 * Test MySQLResultIterator fetching various types of data
 * @author emaphp
 * @group mysql
 * @group iterator
 */
class ResultIteratorTest extends MySQLTest {
	public function testDefault() {
		/////
		$result = self::$conn->query("SELECT user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultIterator($result);
		$expected = array(1, 2, 3, 4, 5);
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current();
	
			$this->assertEquals($expected[$i], (int) $row['user_id']);
			$this->assertEquals($expected[$i], (int) $row[0]);
	
			$ri->next();
		}
	
		$result->free();
	}
	
	public function testArray() {
		/////
		$result = self::$conn->query("SELECT user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultIterator($result);
		$expected = array(1, 2, 3, 4, 5);
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultIterator::AS_ARRAY);
	
			$this->assertEquals($expected[$i], (int) $row['user_id']);
			$this->assertEquals($expected[$i], (int) $row[0]);
	
			$ri->next();
		}
	
		$result->free();
	}
	
	public function testArrayBoth() {
		/////
		$result = self::$conn->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultIterator($result);
		$expected_id = array(1, 2, 3, 4, 5);
		$expected_name = array('jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael');
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultIterator::AS_ARRAY, ArrayType::BOTH);
	
			$this->assertEquals($expected_name[$i], $row['user_name']);
			$this->assertEquals($expected_name[$i], $row[0]);
	
			$this->assertEquals($expected_id[$i], (int) $row['user_id']);
			$this->assertEquals($expected_id[$i], (int) $row[1]);
	
			$ri->next();
		}
	
		$result->free();
	}
	
	public function testArrayAssoc() {
		/////
		$result = self::$conn->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultIterator($result);
		$expected_id = array(1, 2, 3, 4, 5);
		$expected_name = array('jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael');
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultIterator::AS_ARRAY, ArrayType::ASSOC);
	
			$this->assertEquals($expected_name[$i], $row['user_name']);
			$this->assertArrayNotHasKey(0, $row);
	
			$this->assertEquals($expected_id[$i], (int) $row['user_id']);
			$this->assertArrayNotHasKey(1, $row);
	
			$ri->next();
		}
	
		$result->free();
	}
	
	public function testArrayNum() {
		/////
		$result = self::$conn->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultIterator($result);
		$expected_id = array(1, 2, 3, 4, 5);
		$expected_name = array('jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael');
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultIterator::AS_ARRAY, ArrayType::NUM);
	
			$this->assertArrayNotHasKey('user_name', $row);
			$this->assertEquals($expected_name[$i], $row[0]);
	
			$this->assertArrayNotHasKey('user_id', $row);
			$this->assertEquals($expected_id[$i], (int) $row[1]);
	
			$ri->next();
		}
	
		$result->free();
	}
	
	public function testFetchArray() {
		/////
		$result = self::$conn->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultIterator($result);
		$expected_id = array(1, 2, 3, 4, 5);
		$expected_name = array('jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael');
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->fetchArray();
	
			$this->assertEquals($expected_name[$i], $row['user_name']);
			$this->assertEquals($expected_name[$i], $row[0]);
	
			$this->assertEquals($expected_id[$i], (int) $row['user_id']);
			$this->assertEquals($expected_id[$i], (int) $row[1]);
	
			$ri->next();
		}
	
		$result->free();
	}
	
	public function testFetchObject() {
		/////
		$result = self::$conn->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultIterator($result);
		$expected_id = array(1, 2, 3, 4, 5);
		$expected_name = array('jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael');
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->fetchObject();
	
			$this->assertInstanceOf('stdClass', $row);
			$this->assertEquals($expected_name[$i], $row->user_name);
			$this->assertEquals($expected_id[$i], (int) $row->user_id);
	
			$ri->next();
		}
	
		$result->free();
	}
}

?>