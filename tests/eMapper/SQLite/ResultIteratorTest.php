<?php
namespace eMapper\SQLite;

use eMapper\AbstractResultIteratorTest;

/**
 * Test SQLiteResultIterator mapping to different types of row
 * @author emaphp
 * @group sqlite
 * @group iterator
 */
class ResultIteratorTest extends AbstractResultIteratorTest {
	use SQLiteConfig;
	
	protected function query($query) {
		return $this->conn->query($query);
	}
}
?>