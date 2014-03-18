<?php
namespace eMapper\PostgreSQL;

/**
 * Tests binary insertion in a PostgreSQL database
 * @author emaphp
 * @group postgre
 * @group blob
 */
class BlobInsertTest extends PostgreSQLTest {
	public function testInteger() {
		$int = self::$mapper->type('integer')->query("SELECT 1");
		$this->assertInternalType('integer', $int);
		$this->assertEquals(1, $int);
	}
	
	public function testInsertion() {
		$this->markTestIncomplete(
				'This test has not been implemented yet.'
		);
		
		self::$mapper->query("INSERT INTO users VALUES (%{i}, %{s}, %{s}, %{s}, %{s}, %{x})",
							3, 'jackdoe', '1987-08-10', '2013-08-10 19:57:15', '12:00:00', self::$blob);
	}
}
?>