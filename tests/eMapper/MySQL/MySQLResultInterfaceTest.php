<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\Result\MySQLResultInterface;
use eMapper\Result\ResultInterface;

/**
 * Test MySQLResultInterface class
 * 
 * @author emaphp
 * @group mysql
 */
class MySQLResultInterfaceTest extends MySQLTest {
	public function testArrayBoth() {
		/////
		$result = self::$conn->query("SELECT user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultInterface($result);
		$expected = array(1, 2, 3, 4, 5);
		
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current();
			
			$this->assertEquals($expected[$i], (int) $row['user_id']);
			$this->assertEquals($expected[$i], (int) $row[0]);
			
			$ri->next();
		}
		
		$result->free();
		
		/////
		$result = self::$conn->query("SELECT user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultInterface($result);
		$expected = array(1, 2, 3, 4, 5);
		
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultInterface::AS_ARRAY);
				
			$this->assertEquals($expected[$i], (int) $row['user_id']);
			$this->assertEquals($expected[$i], (int) $row[0]);
				
			$ri->next();
		}
		
		$result->free();
		
		/////
		$result = self::$conn->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultInterface($result);
		$expected_id = array(1, 2, 3, 4, 5);
		$expected_name = array('jdoe', 'okenobi', 'jkirk', 'egoldstein', 'ishmael');
		
		for ($i = 0; $ri->valid(); $i++) {
			$row = $ri->current(ResultInterface::AS_ARRAY, ResultInterface::BOTH);
		
			$this->assertEquals($expected_name[$i], $row['user_name']);
			$this->assertEquals($expected_name[$i], $row[0]);
			
			$this->assertEquals($expected_id[$i], (int) $row['user_id']);
			$this->assertEquals($expected_id[$i], (int) $row[1]);
		
			$ri->next();
		}
		
		$result->free();
		
		/////
		$result = self::$conn->query("SELECT user_name, user_id FROM users ORDER BY user_id ASC");
		$ri = new MySQLResultInterface($result);
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
}
?>