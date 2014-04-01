<?php
namespace eMapper\SQLite;

use eMapper\SQLite\SQLiteTest;

/**
 * Tests inserting a blob into a SQLite database
 * @author emaphp
 * @group sqlite
 * @group blob
 */
class InsertionTest extends SQLiteTest {
	public function setUp() {
		self::$mapper->sql("CREATE TEMP TABLE \"blob_test\" ( \"test_id\" INTEGER NOT NULL, value BLOB);");
	}
	
	public function testInsert() {
		self::$mapper->query("INSERT INTO blob_test VALUES (1, %{blob})", self::$blob);
		$row = self::$mapper->type('obj')->query("SELECT * FROM blob_test WHERE test_id = 1");
		$this->assertEquals(self::$blob, $row->value);
	}
	
	public function tearDown() {
		self::$mapper->sql("DROP TABLE \"blob_test\";");
	}
}

?>