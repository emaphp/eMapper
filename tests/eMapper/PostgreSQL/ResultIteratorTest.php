<?php
namespace eMapper\PostgreSQL;

use eMapper\AbstractResultIteratorTest;

/**
 * Test PostgreSQLResultIterator fetching various types of data
 * @author emaphp
 * @group postgre
 * @group iterator
 */
class ResultIteratorTest extends AbstractResultIteratorTest {
	use PostgreSQLConfig;
	
	protected function query($query) {
		return pg_query($this->conn, $query);
	}
}
?>