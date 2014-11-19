<?php
namespace eMapper\SQLite;

use eMapper\SQLite\SQLiteTest;

/**
 * Tests inserting a blob into a SQLite database
 * @author emaphp
 * @group sqlite
 * @group blob
 */
class InsertionTest extends \PHPUnit_Framework_TestCase {
	use SQLiteConfig;
	
	protected $mapper;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
		$this->mapper->sql("CREATE TEMP TABLE \"blob_test\" ( \"test_id\" INTEGER NOT NULL, value BLOB);");
	}
	
	public function testInsert() {
		$this->mapper->query("INSERT INTO blob_test VALUES (1, %{blob})", $this->getBlob());
		$row = $this->mapper->type('obj')->query("SELECT * FROM blob_test WHERE test_id = 1");
		$this->assertEquals($this->getBlob(), $row->value);
	}
	
	public function tearDown() {
		$this->mapper->sql("DROP TABLE \"blob_test\";");
		$this->mapper->close();
	}
}

?>