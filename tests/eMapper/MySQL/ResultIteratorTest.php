<?php
namespace eMapper\MySQL;

use eMapper\AbstractResultIteratorTest;

/**
 * Test MySQLResultIterator fetching various types of data
 * @author emaphp
 * @group mysql
 * @group iterator
 */
class ResultIteratorTest extends AbstractResultIteratorTest {
	use MySQLConfig;

	protected function query($query) {
		return $this->conn->query($query);
	}
	
	public function close() {
		$this->conn->close();
	}
}
?>