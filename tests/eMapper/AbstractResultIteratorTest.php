<?php
namespace eMapper;

use eMapper\Result\ResultIterator;
use eMapper\Result\ArrayType;

abstract class AbstractResultIteratorTest extends \PHPUnit_Framework_TestCase {
	protected $conn;
	
	public function setUp() {
		$this->conn = $this->getConnection(); 
	}
	
	protected abstract function query($query);
	protected abstract function close();
	
	public function testDefault() {
		$result = $this->query("SELECT user_id FROM users ORDER BY user_id ASC");
		$ri = $this->getResultIterator($result);
		$expected = [1, 2, 3, 4, 5];
		
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current();
		
			$this->assertEquals($expected[$i], (int) $row['user_id']);
			$this->assertEquals($expected[$i], (int) $row[0]);
		
			$ri->next();
		}
		
		$ri->free();
	}
	
	public function testArray() {
		$result = $this->query("SELECT user_id FROM users ORDER BY user_id ASC");
		$ri = $this->getResultIterator($result);
		$expected = [1, 2, 3, 4, 5];
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultIterator::AS_ARRAY);
	
			$this->assertEquals($expected[$i], (int) $row['user_id']);
			$this->assertEquals($expected[$i], (int) $row[0]);
	
			$ri->next();
		}
	
		$ri->free();
	}
	
	public function testArrayBoth() {
		$result = $this->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = $this->getResultIterator($result);
		$expected_id = [1, 2, 3, 4, 5];
		$expected_name = ['jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael'];
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultIterator::AS_ARRAY, ArrayType::BOTH);
	
			$this->assertEquals($expected_name[$i], $row['user_name']);
			$this->assertEquals($expected_name[$i], $row[0]);
	
			$this->assertEquals($expected_id[$i], (int) $row['user_id']);
			$this->assertEquals($expected_id[$i], (int) $row[1]);
	
			$ri->next();
		}
	
		$ri->free();
	}
	
	public function testArrayAssoc() {
		$result = $this->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = $this->getResultIterator($result);
		$expected_id = [1, 2, 3, 4, 5];
		$expected_name = ['jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael'];
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultIterator::AS_ARRAY, ArrayType::ASSOC);
	
			$this->assertEquals($expected_name[$i], $row['user_name']);
			$this->assertArrayNotHasKey(0, $row);
	
			$this->assertEquals($expected_id[$i], (int) $row['user_id']);
			$this->assertArrayNotHasKey(1, $row);
	
			$ri->next();
		}
	
		$ri->free();
	}
	
	public function testArrayNum() {
		$result = $this->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = $this->getResultIterator($result);
		$expected_id = [1, 2, 3, 4, 5];
		$expected_name = ['jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael'];
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultIterator::AS_ARRAY, ArrayType::NUM);
	
			$this->assertArrayNotHasKey('user_name', $row);
			$this->assertEquals($expected_name[$i], $row[0]);
	
			$this->assertArrayNotHasKey('user_id', $row);
			$this->assertEquals($expected_id[$i], (int) $row[1]);
	
			$ri->next();
		}
	
		$ri->free();
	}
	
	public function testFetchArray() {
		$result = $this->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = $this->getResultIterator($result);
		$expected_id = [1, 2, 3, 4, 5];
		$expected_name = ['jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael'];
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->fetchArray();
	
			$this->assertEquals($expected_name[$i], $row['user_name']);
			$this->assertEquals($expected_name[$i], $row[0]);
	
			$this->assertEquals($expected_id[$i], (int) $row['user_id']);
			$this->assertEquals($expected_id[$i], (int) $row[1]);
	
			$ri->next();
		}
	
		$ri->free();
	}
	
	public function testFetchObject() {
		$result = $this->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = $this->getResultIterator($result);
		$expected_id = [1, 2, 3, 4, 5];
		$expected_name = ['jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael'];
	
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->fetchObject();
	
			$this->assertInstanceOf('stdClass', $row);
			$this->assertEquals($expected_name[$i], $row->user_name);
			$this->assertEquals($expected_id[$i], (int) $row->user_id);
	
			$ri->next();
		}
	
		$ri->free();
	}
	
	public function tearDown() {
		$this->close();
	}
}
?>